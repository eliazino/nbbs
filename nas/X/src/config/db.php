<?php
class db{
	/*
		Config
	*/
	private $dbhost = '127.0.0.1';
	private $dbuser = 'yandgafn';
	private $dbpass = 'u8zHv7FNaSG5';
	private $dbname = 'yandgafn_s8';
	/*
		Connection
	*/
	public function connect(){
		$dbh = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}
	function verifyKey($username, $key, $usertype){
		if(is_array($usertype)){
			$utype = $usertype[0];
			$access = $usertype[1];
			$sql = "select count(*) from admin where (username='".$username."' or id='".$username."') and sessionKey='".$key."'";
		}else{
			if($usertype == 'admin'){
				$sql = "select count(*) from admin where (username='".$username."' or id='".$username."') and sessionKey='".$key."'";
			}else{
				$sql = "select count(*) from studentprofile where (username='".$username."' or id='".$username."') and sessionKey='".$key."'";
			}
		}
		$dbn = $this->connect();
		$qr = $dbn->query($sql);
		if ($qr->fetchColumn() > 0) {
			return true;
			}else{
				return false;
			}
	}
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
		$header = "From: StudentAccommod8 <no-reply@studentaccommod8.com>\r\n"; 
			$header .= "To: ".$to." \r\n"; 
			$header.= "MIME-Version: 1.0\r\n"; 
			$header.= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
			$header.= "X-Priority: 1\r\n";
			if(is_array($message)){
				$body = $message[0];
				$subject = $message[1];
			}else{
				$subject = "StudentAccommod8 Booking Notice";
				$body = $message;
			}
			if(mail($to,$subject,$body,$header)){
				return true;
			}else{
				return false;
			}
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