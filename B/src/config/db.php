<?php
class db{
	/*
		Config
	*/
	private $dbhost = '127.0.0.1';
	private $dbuser = 'root';
	private $dbpass = '';
	private $dbname = 'bitx';
	/*
		Connection
	*/
	public function connect(){
		$file_db = new PDO('sqlite:messaging.sqlite3');
    // Set errormode to exceptions
    	$file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
 
    // Create new database in memory
    	$memory_db = new PDO('sqlite::memory:');
    // Set errormode to exceptions
   		 $memory_db->setAttribute(PDO::ATTR_ERRMODE, 
                              PDO::ERRMODE_EXCEPTION);
		/*$dbh = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/
		return $file_db;
	}
	/*function verifyKey($username, $key, $usertype){
		if(is_array($usertype)){
			$utype = $usertype[0];
			$access = $usertype[1];
			$sql = "select count(*) from admin where (username='".$username."' or id='".$username."') and publicKey='".$key."' and accessLevel >= $access ";
		}else{
			if($usertype == 'admin'){
				$sql = "select count(*) from admin where (username='".$username."' or id='".$username."') and publicKey='".$key."'";
			}else{
				$sql = "select count(*) from users where (username='".$username."' or id='".$username."') and publicKey='".$key."'";
			}
		}
		$dbn = $this->connect();
		$qr = $dbn->query($sql);
		if ($qr->fetchColumn() > 0) {
			return true;
			}else{
				return false;
			}
	}*/
	function isExist($query){
		$dbn = $this->connect();
		$q = $dbn->query($query);
		if($q->fetchColumn() > 0){
			return true;
		}else{
			return false;
		}
	}
	function sendThis($to, $message){
		$header = "From: BeepAcademy <support@beepxchangeplus.com>\r\n"; 
			$header .= "To: ".$to." \r\n"; 
			$header.= "MIME-Version: 1.0\r\n"; 
			$header.= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
			$header.= "X-Priority: 1\r\n";
			if(is_array($message)){
				$body = $message[0];
				$subject = $message[1];
			}else{
				$subject = "BeepAcademy Progam";
				$body = $message;
			}
			if(mail($to,$subject,$body,$header)){
				return true;
			}else{
				return false;
			}
	}
	function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	function getAllDiscount($mid){
		$retDat = array("discounts"=>[], "sharePerc"=>[], "referer"=>"");
		$a = "select othersDiscount, goldClubDiscount, discount, referalID as referer from merchants where merchants.merchantID = :name";
		$db = $this->connect();
		$f = $db->prepare($a);
		$f->execute(array(':name' => $mid));
		$row = $f->fetchAll();
		if($row){
			$data = json_encode($row, true);
			$data = json_decode($data,true);
			array_push($retDat["discounts"], $data[0]['discount']);
			array_push($retDat["discounts"], $data[0]['goldClubDiscount']);
			array_push($retDat["discounts"], $data[0]['othersDiscount']);
			$retDat["referer"] = $data[0]['referer'];
		}
		$a = "select * from dividend order by id desc limit 1";
		$db = $this->connect();
		$f = $db->prepare($a);
		$f->execute();
		$row = $f->fetchAll();
		if($row){
			$data = json_encode($row, true);
			$data = json_decode($data,true);
			array_push($retDat["sharePerc"], $data[0]['issuer']);
			array_push($retDat["sharePerc"], $data[0]['beepPay']);
			array_push($retDat["sharePerc"], $data[0]['referer']);
		}
		return $retDat;
	}
	function selectFromQuery($query){
		$db = $this->connect();
		$f = $db->prepare($query);
		$f->execute();
		$row = $f->fetchAll();
		if($row){
			$data = json_encode($row, true);
		}else{
			$data = "[]";
		}
		return $data;
	}
	/*public function createAdmin($binds){
		$stmt = $dbh->prepare("INSERT INTO admin (username,email,fullname, password,phone,publicKey,imageDir) VALUES (?,?,?,?,?,?,?)");
		$stmt->bindParam(1, $binds=>$username);
		$stmt->bindParam(2, $email);
		$stmt->bindParam(3, $fullname);
		$stmt->bindParam(4, $password);
		$stmt->bindParam(5, $phone);
		$stmt->bindParam(6, $public);
		$stmt->bindParam(7, $imageDir);
		return $stmt;
	}*/
}
?>