<?php
?>
<div class="col-lg-12">
	<div class="card">
		<div class="card-body">
			<form action="" id="kullanıcıyı_yönet">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<div class="form-group">
							<label for="isim" class="control-label">İsim</label>
							<input type="text" name="isim" id ="isim" class="form-control form-control-sm" required value="<?php echo isset($isim) ? $isim : '' ?>">
						</div>
						<div class="form-group">
							<label for="soyisim" class="control-label">Soyisim</label>
							<input type="text" name="soyisim" id ="soyisim" class="form-control form-control-sm" required value="<?php echo isset($soyisim) ? $soyisim : '' ?>">
						</div>
						<?php if($_SESSION['login_pozisyon'] == 1): ?>
						<div class="form-group">
							<label for="pozisyon" class="control-label">Pozisyon</label>
							<select name="pozisyon" id="pozisyon" class="custom-select custom-select-sm">
								<option value="3" <?php echo isset($pozisyon) && $pozisyon == 3 ? 'selected' : '' ?>>Personel</option>
								<option value="2" <?php echo isset($pozisyon) && $pozisyon == 2 ? 'selected' : '' ?>>Proje Yöneticisi</option>
								<option value="1" <?php echo isset($pozisyon) && $pozisyon == 1 ? 'selected' : '' ?>>Yönetici</option>
							</select>
						</div>
						<?php else: ?>
							<input type="hidden" name="pozisyon" value="3">
						<?php endif; ?>
					</div>
					<div class="col-md-6">
						
						<div class="form-group">
							<label class="control-label">Email</label>
							<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
							<small id="#msg"></small>
						</div>
						<div class="form-group">
							<label class="control-label">Şifre</label>
							<input type="password" class="form-control form-control-sm" name="sifre" <?php echo !isset($id) ? "required":'' ?>>
							<small><i><?php echo isset($id) ? "Leave this blank if you dont want to change you password":'' ?></i></small>
						</div>
						<div class="form-group">
							<label class="label control-label">Şifreyi Onayla</label>
							<input type="password" class="form-control form-control-sm" name="cpass" <?php echo !isset($id) ? 'required' : '' ?>>
							<small id="pass_match" data-status=''></small>
						</div>
					</div>
				</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Kaydet</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=kullanıcı_listesi'">İptal</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	$('[name="sifre"],[name="cpass"]').keyup(function(){
		var pass = $('[name="sifre"]').val()
		var cpass = $('[name="cpass"]').val()
		if(cpass == '' ||pass == ''){
			$('#pass_match').attr('data-status','')
		}else{
			if(cpass == pass){
				$('#pass_match').attr('data-status','1').html('<i class="text-success">Şifre Eşleşti.</i>')
			}else{
				$('#pass_match').attr('data-status','2').html('<i class="text-danger">Şifreler eşleşmiyor.</i>')
			}
		}
	})
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#kullanıcıyı_yönet').submit(function(e){
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if($('[name="sifre"]').val() != '' && $('[name="cpass"]').val() != ''){
			if($('#pass_match').attr('data-status') != 1){
				if($("[name='sifre']").val() !=''){
					$('[name="sifre"],[name="cpass"]').addClass("border-danger")
					end_load()
					return false;
				}
			}
		}
		$.ajax({
			url:'ajax.php?action=kullanıcıyı_kaydet',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Veriler başarıyla kaydedildi.',"success");
					setTimeout(function(){
						location.replace('index.php?page=kullanıcı_listesi')
					},750)
				}else if(resp == 2){
					$('#msg').html("<div class='alert alert-danger'>E-posta zaten mevcut.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>