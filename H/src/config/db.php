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
	function isExist($query, $r = true){
		$dbn = $this->connect();
		//$q = $dbn->query($query);
		$fi = $dbn->prepare($query);
		$fi->execute();
		$row = $fi->fetchAll();
		if($row){
			return true;
		}else{
			return false;
		}
		/*die(count($q->fetchColumn()));
		if(!$r){
			$q = $dbn->query($query);
			die($q->fetchColumn());
		}else{
			$q = $dbn->query($query);
		}*/
		/*if($q->fetchColumn() > 0){
			return true;
		}else{
			return false;
		}*/
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
	function findMonthBound($month){
		$first_minute = mktime(0, 0, 0, $month, 1);
    	$last_minute = mktime(23, 59, 0, $month, date('t', $first_minute));
    	$times = array($first_minute, $last_minute);
		return $times;
	}
	function proccessLimit($cardID){
		try{
			$sql = "select count(*) as totalFeed from transactions where cardSerial = :cardSerial and transDate >= :lowerBound and transDate <= :upperBound";
			$thisMonth = date('m',time());
			$month = $this->findMonthBound($thisMonth);
			$db = $this->connect();
			$f = $db->prepare($sql);
			$f->execute(array(':cardSerial' => $cardID, ':lowerBound' => $month[0], ':upperBound' => $month[1]));
			$row = $f->fetchAll();
			if($row){
				$data = json_encode($row, true);
				$data = json_decode($data,true);
				$numTimes = $data[0]['totalFeed'];
				if($numTimes >= 22){
					$sql = "update cards set valid = 0, limitReached = :thisMonth where cardSerial = :cardSerial";
					$f =$db->prepare($sql);
					$f->bindParam(':cardSerial', $cardID);
					$f->bindParam(':thisMonth', $thisMonth);
					$f->execute();
				}else{
					//$sql = "update cards set valid = 1 where cardSerial = :cardSerial";
				}
			}else{

			}
		}catch(Exception $e){
			
		}
	}
	function reverseCards(){
		$lastMonth = (date('m',time()) == 1 )? 12 : date('m',time()) - 1;
		$thisMonth = date('m',time());
		if(time() <= strtotime(date('10-m-Y'))){
			try{
				$sql = "update cards set valid = 0, limitReached = 0 where limitReached != :thisMonth and valid = 0";
				$db = $this->connect();
				$f =$db->prepare($sql);
				$f->bindParam(':cardSerial', $cardID);
				$f->bindParam(':thisMonth', $thisMonth);
				$f->execute();
			}catch(Exception $e){

			}
		}else{

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