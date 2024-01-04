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
	<dl>
		<dt><b class="border-bottom border-primary">Görev</b></dt>
		<dd><?php echo ucwords($gorev) ?></dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Durum</b></dt>
		<dd>
			<?php 
        	if($durum == 1){
		  		echo "<span class='badge badge-secondary'>Beklemede</span>";
        	}elseif($durum == 2){
		  		echo "<span class='badge badge-primary'>Devam Ediyor</span>";
        	}elseif($durum == 3){
		  		echo "<span class='badge badge-success'>Tamamlandı</span>";
        	}
        	?>
		</dd>
	</dl>
	<dl>
		<dt><b class="border-bottom border-primary">Açıklama</b></dt>
		<dd><?php echo html_entity_decode($aciklama) ?></dd>
	</dl>
</div>