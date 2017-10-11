<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;


$app->post('/api/create/issuer', function(Request $req, Response $resp){
	try{
		$uemail = $req->getparam('username');
		$ukey = $req->getParam('publicKey');
		$issuerEmail = $req->getParam('email');
		$issuerName = $req->getParam('name');
		$issuerPhone = $req->getParam('phone');
		$dbn = new db();
		$dbn = $dbn->connect();
		if(isset($issuerEmail) and isset($issuerPhone) and isset($issuerPhone)){
			if(is_numeric($issuerPhone) and strlen($issuerName) > 2){
				$sql = "select count (*) from users where username='".$uemail."' and publicKey='".$ukey."'";
				$check = new db();
				if($check->isExist($sql)){
					$mcrypto = new mcrypt();
					$issuerID = $mcrypto->mCryptThis(time());
					$isdate = time();
					$sql = "insert into `issuers` (issuerID, issuerName, issuerEmail, issuerPhone, issueDate) values (:isid, :isname, :isemail, :isphone, :isdate)";
					$q = $dbn->prepare($sql);
					$q->bindValue(':isid',$issuerID);
					$q->bindValue(':isname',$issuerName);
					$q->bindValue(':isemail',$issuerEmail);
					$q->bindValue(':isphone',$issuerPhone);
					$q->bindValue(':isdate',$isdate);
					$q->execute();
					$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"The issuer has been created","code":"200"}, "content":{"issuerID":"'.$issuerID.'"}}';
				}else{
					$g = '{"error":{"message":"The session have not been found. log in again", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Phone Number, Name or both might be invalid", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"One or more required information isnt set", "status":"1"}}';
		}
	}catch(PDOException $e){
		$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/create/users', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$mcrypto = new mcrypt();
	$publickKey =  $mcrypto->mCryptThis(time());
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	if(isset($username) and isset($password) and strlen($username) > 2 and strlen($password) > 2){
		try{
			$dbn = new db();
			$dbn = $dbn->connect();
			$sql = "insert into users (username, password, publicKey ) values (:username, :password, :key)";
			$q = $dbn->prepare($sql);
			$q->bindValue(':username',$username);
			$q->bindValue(':password', $encryptedPassword);
			$q->bindValue(':key', $publickKey);
			$q->execute();
			$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"The user has been created","code":"200"}, "content":{"publicKey":"'.$publickKey.'"}}';
		}catch(PDOException $e){
			$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"One or more required information isnt correctly set", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/find/user', function (Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$mcrypto = new mcrypt();
	$publickKey =  $mcrypto->mCryptThis(time());
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$sql = "select*from users where username = :username and password = :pass";
	try{
		$dbn = new db();
		$dbn = $dbn->connect();
		$f = $dbn->prepare($sql);
		$f->execute(array(':username' => $username, ':pass'=>$encryptedPassword));
		$row = $f->fetch();
		if($row){
			$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Login successful", "status":"200"}, "content":{"publicKey":"'.$publickKey.'", "username":"'.$username.'"}}';
		}else{
			$g = '{"error":{"message":"Login credentials match not found", "status":"1"}}';
		}
	}catch(PDOException $ex){
		$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/addLog/for/{issuer}', function(Request $req, Response $resp){
	$issuerID = $req->getattribute('issuer');
	$tdate = $req->getParam('transDate');
	$tcard = $req->getParam('cardSerial');
	$tcost = $req->getParam('transCost');
	$tbal = $req->getParam('cardBalance');
	$syncDate = time();
	if(isset($issuerID)){
		$sql = "insert into transactions (cardSerial, transactionDate, issuerID, debitedValue, balanceValue, syncDate) values (:card, :tdate, :iID, :dvalue, :bal, :sync)";
		try{
			$dbn = new db();
			$dbn = $dbn->connect();
			$count = 0;
			if(isset($tcard) and isset($tdate) and isset($tcost) and isset($tbal)){
				$cardDatum = explode(",",$tcard);
				$dateDatum = explode(",",$tdate);
				$costDatum = explode(",",$tcost);
				$tbalDatum = explode(",",$tbal);
				$counter = 0;
				$success = 0;
				$failed = 0;
				while($counter < count($cardDatum)){
					$f = $dbn->prepare($sql);
					$f->bindValue(':card', trim($cardDatum[$counter]));
					$f->bindValue(':tdate', trim($dateDatum[$counter]));
					$f->bindValue(':iID', $issuerID);
					$f->bindValue(':dvalue', trim($costDatum[$counter]));
					$f->bindValue(':bal', trim($tbalDatum[$counter]));
					$f->bindValue(':sync', $syncDate);
					if($f->execute()){
						$success++;
					}else{
						$failed++;
					}
					$counter++;
				}
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"LOG session succesful.", "status":"200"}, "content":{"issuer":"'.$issuerID.'", "failed":"'.$failed.'", "succesful":"'.$success.'"}}';
			}else{
				$g = '{"error":{"message":"All Parameters are required. ", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$err = str_replace('"','\'', $ex->getMessage());
			$g = '{"error":{"message":"'.$err.'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Issuer ID is required. ", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/get/transactions/{issuer}', function(Request $req, Response $resp){
	$issuer = $req->getAttribute('issuer');
	$headers = $req->getHeaders();    
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
		$publicKey  = $headers['HTTP_PUBLICKEY'][0];
    	$username  = $headers['HTTP_USERNAME'][0];
		if($issuer == "-"){
			$sql = "select issuerName, issueDate, isValid, issuerEmail, issuerPhone, cardSerial, transactionDate, transactions.issuerID, debitedValue, balanceValue, syncDate from transactions left join issuers on issuers.issuerID = transactions.issuerID";			
		}else{
			$sql = "select issuerName, issueDate, isValid, issuerEmail, issuerPhone, cardSerial, transactionDate, transactions.issuerID, debitedValue, balanceValue, syncDate from transactions left join issuers on issuers.issuerID = transactions.issuerID where transactions.issuerID = '".$issuer."'";
		}
		try{
			$dbn = new db();
			$dbn = $dbn->connect();
			$stmt = $dbn->query($sql);
			$data = $stmt->fetchAll(PDO::FETCH_OBJ);
			$dbn = null;
			$data = json_encode($data);
			$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
		}catch(PDOException $e){
			$err = str_replace('"','\'', $e->getMessage());
			$g = '{"error":{"message":"'.$err.'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Remember, username and publicKey credentials are to be logged in the header. ", "status":"1"}}';
	}
	return $resp->withStatus(200)
     		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/get/issuer/{issuerID}', function(Request $req, Response $resp){
	$issuer = $req->getAttribute('issuerID');
	$headers = $req->getHeaders();    
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
		$publicKey  = $headers['HTTP_PUBLICKEY'][0];
    	$username  = $headers['HTTP_USERNAME'][0];
		if($issuer == "-"){
			$sql = "select issuerName, issueDate, isValid, issuerEmail, issuerPhone, issuerID from issuers order by id desc";			
		}else{
			$sql = "select issuerName, issueDate, isValid, issuerEmail, issuerPhone, issuerID from issuers where issuers.issuerID = '".$issuer."'";
		}
		try{
			$dbn = new db();
			$dbn = $dbn->connect();
			$stmt = $dbn->query($sql);
			$data = $stmt->fetchAll(PDO::FETCH_OBJ);
			$dbn = null;
			$data = json_encode($data);
			$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
		}catch(PDOException $e){
			$err = str_replace('"','\'', $e->getMessage());
			$g = '{"error":{"message":"'.$err.'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Remember, username and publicKey credentials are to be logged in the header. ", "status":"1"}}';
	}
	return $resp->withStatus(200)
     		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->put('/api/set/{validity}/{issuerID}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('publicKey');
	$issuer = $req->getAttribute('issuerID');
	$validity = $req->getAttribute('validity');
	$sql = "select count (*) from users where username='".$username."' and publicKey='".$password."'";
	$check = new db();
	if($check->isExist($sql)){
		if(isset($validity) and ($validity == 1 or validity == 0)){
			if(isset($issuer)){
				try{
					$sql = "update issuers set isvalid = :valid where issuerID = :ID";
					$dbn = new db();
					$dbn = $dbn->connect();
					$f = $dbn->prepare($sql);
					$f->bindValue(':valid',$validity);
					$f->bindValue(':ID', $issuer);
					$f->execute();
					$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"issuerID":"'.$issuer.'", "validity":"'.$validity.'"}}';
				}catch(PDOException $e){
					$err = str_replace('"','\'', $e->getMessage());
					$g = '{"error":{"message":"'.$err.'", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"The issuer ID is invalid", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Error sending validity. Use either 0 or 1 to block or allow", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Login credentials match not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
     		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
?>