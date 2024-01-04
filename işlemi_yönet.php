<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM calisan_etkinligi where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="işlemi-yönet">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="proje_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<div class="col-lg-12">
			<div class="row">
				<div class="col-md-5">
					<?php if(!isset($_GET['tid'])): ?>
					 <div class="form-group">
		              <label for="" class="control-label">Görev Adı</label>
		              <select class="form-control form-control-sm select2" name="gorev_id">
		              	<option></option>
		              	<?php 
		              	$tasks = $conn->query("SELECT * FROM gorevler where proje_id = {$_GET['pid']} order by gorev asc ");
		              	while($row= $tasks->fetch_assoc()):
		              	?>
		              	<option value="<?php echo $row['id'] ?>" <?php echo isset($gorev_id) && $gorev_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['gorev']) ?></option>
		              	<?php endwhile; ?>
		              </select>
		            </div>
		            <?php else: ?>
					<input type="hidden" name="gorev_id" value="<?php echo isset($_GET['tid']) ? $_GET['tid'] : '' ?>">
		            <?php endif; ?>
					<div class="form-group">
						<label for="">Konu</label>
						<input type="text" class="form-control form-control-sm" name="konu" value="<?php echo isset($konu) ? $konu : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Tarih</label>
						<input type="date" class="form-control form-control-sm" name="tarih" value="<?php echo isset($tarih) ? date("Y-m-d",strtotime($tarih)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Başlangıç Tarihi</label>
						<input type="time" class="form-control form-control-sm" name="baslangic_tarihi" value="<?php echo isset($baslangic_tarihi) ? date("H:i",strtotime("2020-01-01 ".$baslangic_tarihi)) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="">Bitiş Tarihi</label>
						<input type="time" class="form-control form-control-sm" name="bitis_tarihi" value="<?php echo isset($bitis_tarihi) ? date("H:i",strtotime("2020-01-01 ".$bitis_tarihi)) : '' ?>" required>
					</div>
				</div>
				<div class="col-md-7">
					<div class="form-group">
						<label for="">Yorum/İlerleme Açıklaması</label>
						<textarea name="yorum" id="" cols="30" rows="10" class="summernote form-control" required="">
							<?php echo isset($yorum) ? $yorum : '' ?>
						</textarea>
					</div>
				</div>
			</div>
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
     $('.select2').select2({
	    placeholder:"Lütfen burayı seçin",
	    width: "100%"
	  });
     })
    $('#işlemi-yönet').submit(function(e){
    	e.preventDefault()
    	start_load()
    	$.ajax({
    		url:'ajax.php?action=ilerlemeyi_kaydet',
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