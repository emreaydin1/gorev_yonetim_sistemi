<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=yeni_proje"><i class="fa fa-plus"></i>Yeni Proje Ekle</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Proje</th>
						<th>Görev</th>
						<th>Proje Başlangıç Tarihi</th>
						<th>Proje Bitiş Tarihi</th>
						<th>Proje Durumu</th>
						<th>Görev Durumu</th>
						<th>İşlem</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$where = "";
					if($_SESSION['login_pozisyon'] == 2){
						$where = " where p.yonetici_id = '{$_SESSION['login_id']}' ";
					}elseif($_SESSION['login_pozisyon'] == 3){
						$where = " where concat('[',REPLACE(p.calisan_id,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
					}
					
					$stat = array("Askıda","Başladı","Devam Ediyor","Beklemede","Gecikti","Tamamlandı");
					$qry = $conn->query("SELECT t.*,p.proje_adi as pproje_adi,p.baslangic_tarihi,p.durum as pdurum, p.bitis_tarihi,p.id as pid FROM gorevler t inner join projeler p on p.id = t.proje_id $where order by p.proje_adi asc");
					if (!$qry) {
						echo "Sorgu hatası: " . $conn->error;
					} else {
						while ($row = $qry->fetch_assoc()) {
							$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
							unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
							$desc = strtr(html_entity_decode($row['aciklama']), $trans);
							$desc = str_replace(array("<li>", "</li>"), array("", ", "), $desc);
							$tprog = $conn->query("SELECT * FROM gorevler where proje_id = '{$row['pid']}'")->num_rows;
							$cprog = $conn->query("SELECT * FROM gorevler where proje_id = '{$row['pid']}' and durum = 3")->num_rows;
					
							$prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
							$prog = $prog > 0 ? number_format($prog, 2) : $prog;
							$prod = $conn->query("SELECT * FROM calisan_etkinligi where proje_id = {$row['pid']}")->num_rows;
					
							if ($row['pdurum'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['baslangic_tarihi'])) {
								if ($prod > 0 || $cprog > 0) {
									$row['pdurum'] = 2;
								} else {
									$row['pdurum'] = 1;
								}
							} elseif ($row['pdurum'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['bitis_tarihi'])) {
								$row['pdurum'] = 4;
							}
					
					
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td>
							<p><b><?php echo ucwords($row['pproje_adi']) ?></b></p>
						</td>
						<td>
							<p><b><?php echo ucwords($row['gorev']) ?></b></p>
							<p class="truncate"><?php echo strip_tags($desc) ?></p>
						</td>
						<td><b><?php echo date("M d, Y",strtotime($row['baslangic_tarihi'])) ?></b></td>
						<td><b><?php echo date("M d, Y",strtotime($row['bitis_tarihi'])) ?></b></td>
						<td class="text-center">
							<?php
							  if($stat[$row['pdurum']] =='Askıda'){
							  	echo "<span class='badge badge-secondary'>{$stat[$row['pdurum']]}</span>";
							  }elseif($stat[$row['pdurum']] =='Başladı'){
							  	echo "<span class='badge badge-primary'>{$stat[$row['pdurum']]}</span>";
							  }elseif($stat[$row['pdurum']] =='Devam Ediyor'){
							  	echo "<span class='badge badge-info'>{$stat[$row['pdurum']]}</span>";
							  }elseif($stat[$row['pdurum']] =='Beklemede'){
							  	echo "<span class='badge badge-warning'>{$stat[$row['pdurum']]}</span>";
							  }elseif($stat[$row['pdurum']] =='Gecikti'){
							  	echo "<span class='badge badge-danger'>{$stat[$row['pdurum']]}</span>";
							  }elseif($stat[$row['pdurum']] =='Tamamlalndı'){
							  	echo "<span class='badge badge-success'>{$stat[$row['pdurum']]}</span>";
							  }
							?>
						</td>
						<td>
                        	<?php 
                        	if($row['durum'] == 1){
						  		echo "<span class='badge badge-secondary'>Beklemede</span>";
                        	}elseif($row['durum'] == 2){
						  		echo "<span class='badge badge-primary'>Devam Ediyor</span>";
                        	}elseif($row['durum'] == 3){
						  		echo "<span class='badge badge-success'>Tamamlandı</span>";
                        	}
                        	?>
                        </td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      İşlem
		                    </button>
			                    <div class="dropdown-menu" style="">
			                      <a class="dropdown-item yeni_etkinlik" data-pid = '<?php echo $row['pid'] ?>' data-tid = '<?php echo $row['id'] ?>'  data-task = '<?php echo ucwords($row['gorev']) ?>'  href="javascript:void(0)">İlerleme Ekle</a>
								</div>
						</td>
					</tr>	
					<?php
    } // while döngüsü kapatılır
} // else bloğu kapatılır
?>
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
		$(document).on('click', '.yeni_etkinlik', function(){
		uni_modal("<i class='fa fa-plus'></i> Yeni İlerleme: "+$(this).attr('data-task'),"işlemi_yönet.php?pid="+$(this).attr('data-pid')+"&tid="+$(this).attr('data-tid'),'large')
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
					alert_toast("Veriler Başarıyla Silindi",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>