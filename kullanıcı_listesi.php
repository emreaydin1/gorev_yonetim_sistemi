<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=yeni_kullanıcı"><i class="fa fa-plus"></i>Kullanıcı Ekle</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>İsim</th>
						<th>Email</th>
						<th>Pozisyon</th>
						<th>İşlem</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$type = array('',"Yönetici","Proje Yöneticisi","Personel");
					$qry = $conn->query("SELECT *,concat(isim,' ',soyisim) as isim FROM calisanlar order by concat(isim,' ',soyisim) asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['isim']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td><b><?php echo $type[$row['pozisyon']] ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      İşlem
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item kullanıcıyı_görüntüle" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Görüntüle</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=kullanıcıyı_düzenle&id=<?php echo $row['id'] ?>">Düzenle</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item kullanıcıyı_sil" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Sil</a>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	$('.kullanıcıyı_görüntüle').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> Kullanıcı Detayı","kullanıcıyı_görüntüle.php?id="+$(this).attr('data-id'))
	})
	$('.kullanıcıyı_sil').click(function(){
	_conf("Bu kullanıcı silinecek.","kullanıcıyı_sil",[$(this).attr('data-id')])
	})
	})
	function kullanıcıyı_sil($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=kullanıcıyı_sil',
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