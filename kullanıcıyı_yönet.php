<?php 
include('db_connect.php');
session_start();
if(isset($_GET['id'])){
$user = $conn->query("SELECT * FROM calisanlar where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container-fluid">
	<div id="msg"></div>
	
	<form action="" id="manage-user">	
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">İsim</label>
			<input type="text" name="isim" id="isim" class="form-control" value="<?php echo isset($meta['isim']) ? $meta['isim']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="name">Soyisim</label>
			<input type="text" name="soyisim" id="soyisim" class="form-control" value="<?php echo isset($meta['soyisim']) ? $meta['soyisim']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="email">Email</label>
			<input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" required  autocomplete="off">
		</div>
		<div class="form-group">
			<label for="sifre">Şifre</label>
			<input type="password" name="sifre" id="sifre" class="form-control" value="" autocomplete="off">
			<small><i>Şifreyi değiştirmek istemiyorsanız burayı boş bırakın.</i></small>
		</div>
	</form>
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
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=kullanıcıyı_düzenle',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp ==1){
					alert_toast("Veriler başarıyla kaydedildi",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}else{
					$('#msg').html('<div class="alert alert-danger">Kullanıcı adı mevcut</div>')
					end_load()
				}
			}
		})
	})

</script>