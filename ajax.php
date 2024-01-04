<?php
ob_start();
date_default_timezone_set("Europe/Istanbul");

$action = $_GET['action'];
include 'admin.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}

if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
} 
if($action == 'kayıt'){
	$save = $crud->kayıt();
	if($save)
		echo $save;
}
if($action == 'kullanıcıyı_kaydet'){
	$save = $crud->kullanıcıyı_kaydet();
	if($save)
		echo $save;
}
if($action == 'kullanıcıyı_düzenle'){
	$save = $crud->kullanıcıyı_düzenle();
	if($save)
		echo $save;
}
if($action == 'kullanıcıyı_sil'){
	$save = $crud->kullanıcıyı_sil();
	if($save)
		echo $save;
}
if($action == 'projeyi_kaydet'){
	$save = $crud->projeyi_kaydet();
	if($save)
		echo $save;
}
if($action == 'projeyi_sil'){
	$save = $crud->projeyi_sil();
	if($save)
		echo $save;
}
if($action == 'görevi_kaydet'){
	$save = $crud->görevi_kaydet();
	if($save)
		echo $save;
}
if($action == 'görevi_sil'){
	$save = $crud->görevi_sil();
	if($save)
		echo $save;
}
if($action == 'ilerlemeyi_kaydet'){
	$save = $crud->ilerlemeyi_kaydet();
	if($save)
		echo $save;
}
if($action == 'ilerlemeyi_sil'){
	$save = $crud->ilerlemeyi_sil();
	if($save)
		echo $save;
}
if($action == 'raporu_al'){
	$get = $crud->raporu_al();
	if($get)
		echo $get;
}
ob_end_flush();
?>
