<?php if(!isset($conn)){ include 'db_connect.php'; } ?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-project">

        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="proje_adi" class="control-label">İsim</label>
					<input type="text" class="form-control form-control-sm" name="proje_adi" id="proje_adi"  value="<?php echo isset($proje_adi) ? $proje_adi : '' ?>">
				</div>
			</div>
          	<div class="col-md-6">
				<div class="form-group">
					<label for="durum">Durum</label>
					<select name="durum" id="durum" class="custom-select custom-select-sm">
						<option value="0" <?php echo isset($durum) && $durum == 0 ? 'selected' : '' ?>>Askıda</option>
						<option value="2" <?php echo isset($durum) && $durum == 2 ? 'selected' : '' ?>>Devam Ediyor</option>
						<option value="5" <?php echo isset($durum) && $durum == 5 ? 'selected' : '' ?>>Tamamlandı</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Başlangıç Tarihi</label>
              <input type="tarih" class="form-control form-control-sm" autocomplete="off" name="baslangic_tarihi" value="<?php echo isset($baslangic_tarihi) ? date("Y-m-d",strtotime($baslangic_tarihi)) : '' ?>">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Bitiş Tarihi</label>
              <input type="tarih" class="form-control form-control-sm" autocomplete="off" name="bitis_tarihi" value="<?php echo isset($bitis_tarihi) ? date("Y-m-d",strtotime($bitis_tarihi)) : '' ?>">
            </div>
          </div>
		</div>
        <div class="row">
        	<?php if($_SESSION['login_pozisyon'] == 1 ): ?>
           <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Proje Yöneticisi</label>
              <select class="form-control form-control-sm select2" name="yonetici_id">
              	<option></option>
              	<?php 
              	$managers = $conn->query("SELECT *,concat(isim,' ',soyisim) as calisan FROM calisanlar where pozisyon = 2 order by concat(isim,' ',soyisim) asc ");
              	while($row= $managers->fetch_assoc()):
              	?>
              	<option value="<?php echo $row['id'] ?>" <?php echo isset($yonetici_id) && $yonetici_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['calisan']) ?></option>
              	<?php endwhile; ?>
              </select>
            </div>
          </div>
      <?php else: ?>
      	<input type="hidden" name="yonetici_id" value="<?php echo $_SESSION['login_id'] ?>">
      <?php endif; ?>
          <div class="col-md-6">
            <div class="form-group">
              <label for="" class="control-label">Proje Takım Üyeleri</label>
              <select class="form-control form-control-sm select2" multiple="multiple" name="calisan_id[]">
              	<option></option>
              	<?php 
              	$employees = $conn->query("SELECT *,concat(isim,' ',soyisim) as calisan FROM calisanlar where pozisyon = 3 order by concat(isim,' ',soyisim) asc ");
              	while($row= $employees->fetch_assoc()):
              	?>
              	<option value="<?php echo $row['id'] ?>" <?php echo isset($calisan_id) && in_array($row['id'],explode(',',$calisan_id)) ? "selected" : '' ?>><?php echo ucwords($row['calisan']) ?></option>
              	<?php endwhile; ?>
              </select>
            </div>
          </div>
        </div>
		<div class="row">
			<div class="col-md-10">
				<div class="form-group">
					<label for="" class="control-label">Açıklama</label>
					<textarea name="aciklama" id="" cols="30" rows="10" class="summernote form-control">
						<?php echo isset($aciklama) ? $aciklama : '' ?>
					</textarea>
				</div>
			</div>
		</div>
        </form>
    	</div>
    	<div class="card-footer border-top border-info">
    		<div class="d-flex w-100 justify-content-center align-items-center">
    			<button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-project">Kaydet</button>
    			<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=proje_listesi'">İptal</button>
    		</div>
    	</div>
	</div>
</div>
<script>
    $('#manage-project').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=projeyi_kaydet',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    alert_toast('Veriler başarıyla kaydedildi',"success");
                    setTimeout(function(){
                        location.href = 'index.php?page=proje_listesi'
                    },2000)
                }
            }
        })
    })
</script>