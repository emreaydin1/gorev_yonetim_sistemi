<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
            <?php if($_SESSION['login_pozisyon'] != 3): ?>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=yeni_proje"><i class="fa fa-plus"></i> Yeni Proje Ekle</a>
			</div>
            <?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="35%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Proje</th>
						<th>Başlangıç</th>
						<th>Bitiş</th>
						<th>Durum</th>
						<th>İşlem</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$stat = array("Askıda","Başladı","Devam Ediyor","Beklemede","Gecikti","Tamamlandı");
					$where = "";
					if($_SESSION['login_pozisyon'] == 2){
						$where = " where yonetici_id = '{$_SESSION['login_id']}' ";
					}elseif($_SESSION['login_pozisyon'] == 3){
						$where = " where concat('[',REPLACE(calisan_id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
					}
					$qry = $conn->query("SELECT * FROM projeler $where order by proje_adi asc");
					while($row= $qry->fetch_assoc()):
						$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
						unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
						$desc = strtr(html_entity_decode($row['aciklama']),$trans);
						$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);

					 	$tprog = $conn->query("SELECT * FROM gorevler where proje_id = {$row['id']}")->num_rows;
		                $cprog = $conn->query("SELECT * FROM gorevler where proje_id = {$row['id']} and durum = 3")->num_rows;
						$prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
		                $prog = $prog > 0 ?  number_format($prog,2) : $prog;
		                $prod = $conn->query("SELECT * FROM calisan_etkinligi where proje_id = {$row['id']}")->num_rows;
						if($row['durum'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['baslangic_tarihi'])):
						if($prod  > 0  || $cprog > 0)
		                  $row['durum'] = 2;
		                else
		                  $row['durum'] = 1;
						elseif($row['durum'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['bitis_tarihi'])):
						$row['durum'] = 4;
						endif;
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td>
							<p><b><?php echo ucwords($row['proje_adi']) ?></b></p>
							<p class="truncate"><?php echo strip_tags($desc) ?></p>
						</td>
						<td><b><?php echo date("M d, Y",strtotime($row['baslangic_tarihi'])) ?></b></td>
						<td><b><?php echo date("M d, Y",strtotime($row['bitis_tarihi'])) ?></b></td>
						<td class="text-center">
							<?php
							  if($stat[$row['durum']] =='Askıda'){
							  	echo "<span class='badge badge-secondary'>{$stat[$row['durum']]}</span>";
							  }elseif($stat[$row['durum']] =='Başladı'){
							  	echo "<span class='badge badge-primary'>{$stat[$row['durum']]}</span>";
							  }elseif($stat[$row['durum']] =='Devam Ediyor'){
							  	echo "<span class='badge badge-info'>{$stat[$row['durum']]}</span>";
							  }elseif($stat[$row['durum']] =='Beklemede'){
							  	echo "<span class='badge badge-warning'>{$stat[$row['durum']]}</span>";
							  }elseif($stat[$row['durum']] =='Gecikti'){
							  	echo "<span class='badge badge-danger'>{$stat[$row['durum']]}</span>";
							  }elseif($stat[$row['durum']] =='Tamamlandı'){
							  	echo "<span class='badge badge-success'>{$stat[$row['durum']]}</span>";
							  }
							?>
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      İşlem
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item projeyi_görüntüle" href="./index.php?page=projeyi_görüntüle&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">Görüntüle</a>
		                      <div class="dropdown-divider"></div>
		                      <?php if($_SESSION['login_pozisyon'] != 3): ?>
		                      <a class="dropdown-item" href="./index.php?page=projeyi_düzenle&id=<?php echo $row['id'] ?>">Düzenle</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item projeyi_sil" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Sil</a>
		                  <?php endif; ?>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table p{
		margin: unset !important;
	}
	table td{
		vertical-align: middle !important
	}
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	
	$('.projeyi_sil').click(function(){
	_conf("Bu projeyi silmek istediğinizden emin misiniz?","projeyi_sil",[$(this).attr('data-id')])
	})
	})
	function projeyi_sil($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=projeyi_sil',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Veriler başarıyla silindi?",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>