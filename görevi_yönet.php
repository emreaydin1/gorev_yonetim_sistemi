<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM gorevler where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="işlemi_yönet">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="proje_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<div class="form-group">
			<label for="">Görev</label>
			<input type="text" class="form-control form-control-sm" name="gorev" value="<?php echo isset($gorev) ? $gorev : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="">Açıklama</label>
			<textarea name="aciklama" id="" cols="30" rows="10" class="summernote form-control">
				<?php echo isset($aciklama) ? $aciklama : '' ?>
			</textarea>
		</div>
		<div class="form-group">
			<label for="">Durum</label>
			<select name="durum" id="durum" class="custom-select custom-select-sm">
				<option value="1" <?php echo isset($durum) && $durum == 1 ? 'selected' : '' ?>>Beklemede</option>
				<option value="2" <?php echo isset($durum) && $durum == 2 ? 'selected' : '' ?>>Devam Ediyor</option>
				<option value="3" <?php echo isset($durum) && $durum == 3 ? 'selected' : '' ?>>Tamamlandı</option>
			</select>
		</div>
	</form>
</div>

<script>
	$(document).ready(function(){


	$('.summernote').summernote({
        height: 200,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    })
     })
    
    $('#işlemi_yönet').submit(function(e){
    	e.preventDefault()
    	start_load()
    	$.ajax({
    		url:'ajax.php?action=görevi_kaydet',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Veriler Başarıyla Kaydedildi',"success");
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
    	})
    })
</script>