<?php
include 'db_connect.php';
$stat = array("Askıda","Başladı","Devam Ediyor","Beklemede","Gecikti","Tamamlandı");
$qry = $conn->query("SELECT * FROM projeler where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
$tprog = $conn->query("SELECT * FROM gorevler where proje_id = {$id}")->num_rows;
$cprog = $conn->query("SELECT * FROM gorevler where proje_id = {$id} and durum = 3")->num_rows;
$prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
$prog = $prog > 0 ?  number_format($prog,2) : $prog;
$prod = $conn->query("SELECT * FROM calisan_etkinligi where proje_id = {$id}")->num_rows;
if($durum == 0 && strtotime(date('Y-m-d')) >= strtotime($baslangic_tarihi)):
if($prod  > 0  || $cprog > 0)
  $durum = 2;
else
  $durum = 1;
elseif($durum == 0 && strtotime(date('Y-m-d')) > strtotime($bitis_tarihi)):
$durum = 4;
endif;
$manager = $conn->query("SELECT *,concat(isim,' ',soyisim) as isim FROM calisanlar where id = $yonetici_id");
$manager = $manager->num_rows > 0 ? $manager->fetch_array() : array();
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
								<dt><b class="border-bottom border-primary">Proje Adı</b></dt>
								<dd><?php echo ucwords($proje_adi) ?></dd>
								<dt><b class="border-bottom border-primary">Açıklama</b></dt>
								<dd><?php echo html_entity_decode($aciklama) ?></dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt><b class="border-bottom border-primary">Başlangıç Tarihi</b></dt>
								<dd><?php echo date("F d, Y",strtotime($baslangic_tarihi)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Bitiş Tarihi</b></dt>
								<dd><?php echo date("F d, Y",strtotime($bitis_tarihi)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Durum</b></dt>
								<dd>
									<?php
									  if($stat[$durum] =='Askıda'){
									  	echo "<span class='badge badge-secondary'>{$stat[$durum]}</span>";
									  }elseif($stat[$durum] =='Başladı'){
									  	echo "<span class='badge badge-primary'>{$stat[$durum]}</span>";
									  }elseif($stat[$durum] =='Devam Ediyor'){
									  	echo "<span class='badge badge-info'>{$stat[$durum]}</span>";
									  }elseif($stat[$durum] =='Beklemede'){
									  	echo "<span class='badge badge-warning'>{$stat[$durum]}</span>";
									  }elseif($stat[$durum] =='Gecikti'){
									  	echo "<span class='badge badge-danger'>{$stat[$durum]}</span>";
									  }elseif($stat[$durum] =='Tamamlandı'){
									  	echo "<span class='badge badge-success'>{$stat[$durum]}</span>";
									  }
									?>
								</dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Proje Yöneticisi</b></dt>
								<dd>
									<?php if(isset($manager['id'])) : ?>
									<div class="d-flex align-items-center mt-1">
										<b><?php echo ucwords($manager['isim']) ?></b>
									</div>
									<?php else: ?>
										<small><i>Yönetici Eklenmedi</i></small>
									<?php endif; ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Takım Üyeleri:</b></span>
					<div class="card-tools">
						<!-- <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="manage_team">Manage</button> -->
					</div>
				</div>
				<div class="card-body">
					<ul class="users-list clearfix">
						<?php 
						if(!empty($calisan_id)):
							$members = $conn->query("SELECT *,concat(isim,' ',soyisim) as isim FROM calisanlar where id in ($calisan_id) order by concat(isim,' ',soyisim) asc");
							while($row=$members->fetch_assoc()):
						?>
								<li>
			                        <a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['isim']) ?></a>
			                
		                    	</li>
						<?php 
							endwhile;
						endif;
						?>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Görev Listesi:</b></span>
					<?php if($_SESSION['login_pozisyon'] != 3): ?>
					<div class="card-tools">
						<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="yeni_görev"><i class="fa fa-plus"></i>Yeni Görev</button>
					</div>
				<?php endif; ?>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
					<table class="table table-condensed m-0 table-hover">
						<colgroup>
							<col width="5%">
							<col width="25%">
							<col width="30%">
							<col width="15%">
							<col width="15%">
						</colgroup>
						<thead>
							<th>#</th>
							<th>Görev</th>
							<th>Açıklama</th>
							<th>Durum</th>
							<th>İşlem</th>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$tasks = $conn->query("SELECT * FROM gorevler where proje_id = {$id} order by gorev asc");
							while($row=$tasks->fetch_assoc()):
								$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
								unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
								$desc = strtr(html_entity_decode($row['aciklama']),$trans);
								$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
							?>
								<tr>
			                        <td class="text-center"><?php echo $i++ ?></td>
			                        <td class=""><b><?php echo ucwords($row['gorev']) ?></b></td>
			                        <td class=""><p class="truncate"><?php echo strip_tags($desc) ?></p></td>
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
					                      <a class="dropdown-item görevi_görüntüle" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['gorev'] ?>">Görüntüle</a>
					                      <div class="dropdown-divider"></div>
					                      <?php if($_SESSION['login_pozisyon'] != 3): ?>
					                      <a class="dropdown-item görevi_düzenle" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['gorev'] ?>">Düzenle</a>
					                      <div class="dropdown-divider"></div>
					                      <a class="dropdown-item görevi_sil" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Sil</a>
					                  <?php endif; ?>
					                    </div>
									</td>
		                    	</tr>
							<?php 
							endwhile;
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<b>Üyelerin Etkinliği</b>
					<div class="card-tools">
						<button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="yeni_etkinlik"><i class="fa fa-plus"></i> Yeni İlerleme</button>
					</div>
				</div>
				<div class="card-body">
					<?php 
					$progress = $conn->query("SELECT p.*,concat(u.isim,' ',u.soyisim) as uisim,t.gorev FROM calisan_etkinligi p inner join calisanlar u on u.id = p.calisan_id inner join gorevler t on t.id = p.gorev_id where p.proje_id = $id order by unix_timestamp(p.olusturulma_tarihi) desc ");
					while($row = $progress->fetch_assoc()):
					?>
						<div class="post">

		                      <div class="user-block">
		                      	<?php if($_SESSION['login_id'] == $row['calisan_id']): ?>
		                      	<span class="btn-group dropleft float-right">
								  <span class="btndropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
								    <i class="fa fa-ellipsis-v"></i>
								  </span>
								  <div class="dropdown-menu">
								  	<a class="dropdown-item işlemi_yönet" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['gorev'] ?>">Düzenle</a>
			                      	<div class="dropdown-divider"></div>
				                     <a class="dropdown-item ilerlemeyi_sil" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Sil</a>
								  </div>
								</span>
								<?php endif; ?>
		                    
		                        <span class="username" style="margin-top:40px">
		                          <a href="#"><?php echo ucwords($row['uisim']) ?>[ <?php echo ucwords($row['gorev']) ?> ]</a>
		                        </span>
		                        <span class="aciklama">
		                        	<span class="fa fa-calendar-day"></span>
		                        	<span><b><?php echo date('M d, Y',strtotime($row['tarih'])) ?></b></span>
		                        	<span class="fa fa-user-clock"></span>
                      				<span>Başlangıç: <b><?php echo date('h:i A',strtotime($row['tarih'].' '.$row['baslangic_tarihi'])) ?></b></span>
		                        	<span> | </span>
                      				<span>Bitiş: <b><?php echo date('h:i A',strtotime($row['tarih'].' '.$row['bitis_tarihi'])) ?></b></span>
	                        	</span>

	                        	

		                      </div>
		                      <!-- /.user-block -->
		                      <div>
		                       <?php echo html_entity_decode($row['yorum']) ?>
		                      </div>

		                      <p>
		                        <!-- <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File 1 v2</a> -->
		                      </p>
	                    </div>
	                    <div class="post clearfix"></div>
                    <?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.users-list>li img {
	    border-radius: 50%;
	    height: 67px;
	    width: 67px;
	    object-fit: cover;
	}
	.users-list>li {
		width: 33.33% !important
	}
	.truncate {
		-webkit-line-clamp:1 !important;
	}
</style>
<script>
	$('#yeni_görev').click(function(){
		uni_modal(" <?php echo ucwords($proje_adi) ?> İçin Yeni Görev ","görevi_yönet.php?pid=<?php echo $id ?>","mid-large")
	})
	$('.görevi_düzenle').click(function(){
		uni_modal("Görevi Düzenle: "+$(this).attr('data-task'),"görevi_yönet.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
	})
	$('.görevi_görüntüle').click(function(){
		uni_modal("Görev Detayı","görevi_görüntüle.php?id="+$(this).attr('data-id'),"mid-large")
	})
	$('#yeni_etkinlik').click(function(){
		uni_modal("<i class='fa fa-plus'></i> Yeni İlerleme","işlemi_yönet.php?pid=<?php echo $id ?>",'large')
	})
	$('.işlemi_yönet').click(function(){
		uni_modal("<i class='fa fa-edit'></i> İlerlemeyi Düzenle","işlemi_yönet.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),'large')
		
	})
	$('.ilerlemeyi_sil').click(function(){
	_conf("İlerlemeyi silmek istedğinizden emin misiniz?","ilerlemeyi_sil",[$(this).attr('data-id')])
	})
	$('.görevi_sil').click(function(){
	_conf("Görevi silmek istedğinizden emin misiniz?","görevi_sil",[$(this).attr('data-id')])
	})
	function ilerlemeyi_sil($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=ilerlemeyi_sil',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Veriler başarıyla silindi.",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	function görevi_sil($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=görevi_sil',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Veriler başarıyla silindi.",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>