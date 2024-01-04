<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(isim,' ',soyisim) as isim FROM calisanlar where email = '".$email."' and sifre = '".md5($sifre)."'  ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'sifre' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 2;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	
	function kullanıcıyı_kaydet(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','sifre')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($sifre)){
					$data .= ", sifre=md5('$sifre') ";
		}
		$check = $this->db->query("SELECT * FROM calisanlar where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO calisanlar set $data");
		}else{
			$save = $this->db->query("UPDATE calisanlar set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function kayıt(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM calisanlar where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO calisanlar set $data");

		}else{
			$save = $this->db->query("UPDATE calisanlar set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','sifre')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
				
			return 1;
		}
	}

	function kullanıcıyı_düzenle(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','sifre')) && !is_numeric($k)){
				
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM calisanlar where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(!empty($sifre))
			$data .= " ,sifre=md5('$sifre') ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO calisanlar set $data");
		}else{
			$save = $this->db->query("UPDATE calisanlar set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'sifre' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			return 1;
		}
	}
	function kullanıcıyı_sil(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM calisanlar where id = ".$id);
		if($delete)
			return 1;
	}

	function projeyi_kaydet(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','calisan_id')) && !is_numeric($k)){
				if($k == 'aciklama')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(isset($calisan_id)){
			$data .= ", calisan_id='".implode(',',$calisan_id)."' ";
		}
		// echo $data;exit;
		if(empty($id)){
			$save = $this->db->query("INSERT INTO projeler set $data");
		}else{
			$save = $this->db->query("UPDATE projeler set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function projeyi_sil(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM projeler where id = $id");
		if($delete){
			return 1;
		}
	}
	function görevi_kaydet(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'aciklama')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO gorevler set $data");
		}else{
			$save = $this->db->query("UPDATE gorevler set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function görevi_sil(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM gorevler where id = $id");
		if($delete){
			return 1;
		}
	} 
	
	function ilerlemeyi_kaydet(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'yorum')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$dur = abs(strtotime("2020-01-01 ".$bitis_tarihi)) - abs(strtotime("2020-01-01 ".$baslangic_tarihi));
		$dur = $dur / (60 * 60);
		$data .= ", gecirilen_zaman='$dur' ";
		
		if (empty($id)) {
			$data .= ", calisan_id={$_SESSION['login_id']} ";
			$save = $this->db->query("INSERT INTO calisan_etkinligi SET $data");
		} else {
			$data .= ", calisan_id={$_SESSION['login_id']} "; // Güncellenen kayıtlar için de calisan_id'yi güncelle
			$save = $this->db->query("UPDATE calisan_etkinligi SET $data WHERE id = $id");
		}
		
		if($save){
			return 1;
		}
	}
	
	/*
	function ilerlemeyi_kaydet(){
		extract($_POST);
		$data = "";
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'yorum')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
	
		$dur = 0;
	
		if (strtotime($bitis_tarihi) < strtotime($baslangic_tarihi)) {
			$dur = strtotime($baslangic_tarihi) - strtotime($bitis_tarihi);
		} else {
			$dur = strtotime($bitis_tarihi) - strtotime($baslangic_tarihi);
		}
	
		$hours = floor($dur / 3600);
		$minutes = floor(($dur % 3600) / 60);
		$data .= ", gecirilen_zaman='$hours saat $minutes dakika' ";
	
		if(empty($id)){
			$data .= ", calisan_id={$_SESSION['login_id']} ";
			$save = $this->db->query("INSERT INTO calisan_etkinligi SET $data");
		} else {
			$save = $this->db->query("UPDATE calisan_etkinligi SET $data WHERE id = $id");
		}
	
		if($save){
			return 1;
		}
	}
	*/
	function ilerlemeyi_sil(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM calisan_etkinligi where id = $id");
		if($delete){
			return 1;
		}
	}
	function raporu_al(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between '$date_from' and '$date_to' order by unix_timestamp(t.date_created) desc ");
		while($row= $get->fetch_assoc()){
			$row['date_created'] = date("M d, Y",strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'],2);
			$row['child_price'] = number_format($row['child_price'],2);
			$row['amount'] = number_format($row['amount'],2);
			$data[]=$row;
		}
		return json_encode($data);

	}
}