<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;
//error_reporting(0);

/* Admin Profile management and friends */
/*
*	Everything Admin Profile here
*/
$app->post('/api/admin/create/profile', function (Request $req, Response $resp){
	$uname = $req->getParam('uname');
	$pubKey = $req->getParam('publicKey');
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$email = $req->getParam('email');
	$phone = $req->getParam('phone');
	$fullname = $req->getParam('fullname');
	$gender = $req->getParam('gender');
	$address = $req->getParam('address');
	if(isset($username) and isset($password) and isset($email) and isset($phone) and isset($fullname)){
		if(is_numeric($phone)){
			if(strlen($password) > 6){
				$sql = "select*from admin where username = '$username'";
				$db = new db();
				try{
					if(!$db->isExist($sql)){
						$sql = "select*from admin where email = '$email'";
						if(!$db->isExist($sql)){
							$sql = "select * from admin where username = '".$uname."' and publicKey = '".$pubKey."' and (canteenID is NULL or canteenID = '')";
							if($db->isExist($sql)){
								$sql = "insert into admin (fullname, email, phone, password, username, publicKey, gender, address) values (:fullname, :email, :phone, :passwod, :username, :key, :gender, :address)";
								$mcrypto = new mcrypt();
								$fkey = $mcrypto->mCryptThis(time());
								$isdate = time();
								$key = ' FitSKchgoHOOKing666';
								$string = $key.'34iIlm'.$password.'io9m-';
								$encryptedPassword = hash('sha256', $string);
								$db = $db->connect();
								$f = $db->prepare($sql);
								$f->bindValue(':fullname',$fullname);
								$f->bindValue(':email', $email);
								$f->bindValue(':phone',$phone);
								$f->bindValue(':passwod',$encryptedPassword);
								$f->bindValue(':username',$username);
								$f->bindValue(':key', $fkey);
								$f->bindValue(':gender', $gender);
								$f->bindValue(':address', $address);
								$f->execute();
								$f = new db();
								$data = $f->selectFromQuery("SELECT username, fullname, email, phone, lastseen, gender, address FROM admin WHERE username = '".$username."' and publicKey = '".$key."'");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"The Admin has been created","code":"200"}, "content":{"username":"'.$username.'", "publicKey":"'.$fkey.'", "data":'.$data.'}}';
							}else{
								$g = '{"error":{"message":"The validation failed. Log in again", "status":"1"}}';
							}
						}else{
							$g = '{"error":{"message":"The email exists", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"The username exists", "status":"1"}}';
					}
				}catch(PDOException $ex){
					$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Password might be invalid. Least of 6 chars.", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Phone Number isnt valid", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"One or more required information isnt set", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/dashboard', function(Request $req, Response $resp){
	$a = "select sum(amount) as totalPoints from points";
	$b = "select sum(amount) as totalTransactions from transactions where amount > 0";
	$c = "select count (*) as totalCustomers from customers";
	$headers = $req->getHeaders();
	$dbn = new db();
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			if($dbn->isExist($q)){
				$totalPoints = $dbn->selectFromQuery($a);
				$totalTransactions = $dbn->selectFromQuery($b);
				$totalCustomers = $dbn->selectFromQuery($c);

				/* */
				$allTrans = $dbn->selectFromQuery("select*from transactions");
				$alltopUp = $dbn->selectFromQuery("select*from points");
				$unusedTopUp = $dbn->selectFromQuery("select sum(voucherLeft) as amountLeft from agent");
				$v_t = json_decode($totalPoints, true);
				$v_u = json_decode($unusedTopUp, true);
				$used = $v_t[0]["totalPoints"] - $v_u[0]["amountLeft"];
				$graphEntities = array();
				$graphEntities["yearTransactions"] = array("xVal"=>[], "yVal" => []);
				$graphEntities["yearTopUp"] = array("xVal"=>[], "yVal" => []);
				$graphEntities["monthTransactions"] = array("xVal"=>[], "yVal" => []);
				$graphEntities["vouchers"] = array("used"=>$used, "unused" =>$v_u[0]["amountLeft"]);
				$month = 1;
				while ($month <= date('m')){
					$dataTrans = json_decode($allTrans, true);
					$dataPoints = json_decode($alltopUp, true);
					$thisMonthEnd = $dbn->findMonthBound($month);
					$counter = 0;
					/* Initialize the Values First */
					$graphEntities["yearTransactions"]["xVal"][$month-1] = 0;
					$graphEntities["yearTransactions"]["yVal"][$month-1] = $month;
					$graphEntities["yearTopUp"]["xVal"][$month-1] = 0;
					$graphEntities["yearTopUp"]["yVal"][$month-1] = $month;
					while($counter < count($dataTrans)){
						if($dataTrans[$counter]["transDate"] > $thisMonthEnd[0] and $dataTrans[$counter]["transDate"] < $thisMonthEnd[1]){
							$graphEntities["yearTransactions"]["xVal"][$month-1] = $graphEntities["yearTransactions"]["xVal"][$month-1] + $dataTrans[$counter]["amount"];
						}
						$counter++;
					}
					$counter = 0;
					while($counter < count($dataPoints)){
						if($dataPoints[$counter]["transDate"] > $thisMonthEnd[0] and $dataPoints[$counter]["transDate"] < $thisMonthEnd[1]){
							$graphEntities["yearTopUp"]["xVal"][$month-1] = $graphEntities["yearTopUp"]["xVal"][$month-1] + $dataPoints[$counter]["amount"];
						}
						$counter++;
					}
					$month++;
				}
				$graphEntities = json_encode($graphEntities, true);
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"points":'.$totalPoints.', "totalTransactions":'.$totalTransactions.', "totalCustomers":'.$totalCustomers.', "graphEntities":'.$graphEntities.' }}';
			}else{
				$g = '{"error":{"message":"The Supplied Credentials are invalid. Sign in again", "status":"1"}}';
			}
		}catch(PDOException $e){
			$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"One or more required information isnt set", "status":"1"}}';
	}
	return $resp->withStatus(200)
		->withHeader('Content-Type', 'application/json')
		//->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	    ->write($g);
});
$app->put('/api/admin/update/profile', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$email = $req->getParam('email');
	$phone = $req->getParam('phone');
	$fullname = $req->getParam('fullname');
	$address = $req->getParam('address');
	$gender = $req->getParam('gender');
	if(isset($username) and isset($password) and isset($email) and isset($phone) and isset($fullname)){
		if(is_numeric($fullname)){
			if(strlen($password) > 6){
				$sql = "select*from admin where username = '".$username."'";
				$db = new db();
				try{
					if(!$db->isExist($sql)){
						$sql = "select*from admin where email = '".$email."'";
						if(!$db->isExist($sql)){
							$sql = "update admin set fullname = :fullname, email = :email, address = :address, gender = :gender,  phone = :phone where username = :username and publicKey = :key ";
							$db = $db->connect();
							$f = $db->prepare($sql);
							$f->bindValue(':fullname',$fullname);
							$f->bindValue(':email', $email);
							$f->bindValue(':phone',$phone);
							$f->bindValue(':username', $username);
							$f->bindValue(':key', $key);
							$f->bindValue(':address', $address);
							$f->bindValue(':gender', $gender);
							$f->execute();
							$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"The Admin profile has been updated","code":"200"}, "content":{"username":"'.$username.'", "publicKey":"'.$key.'"}}';
						}else{
							$g = '{"error":{"message":"The email exists", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"The username exists", "status":"1"}}';
					}
				}catch(PDOException $ex){
					$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Password might be invalid. Least of 6 chars.", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Phone Number isnt valid", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"One or more required information isnt set", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->put('/api/admin/update/password', function(Request $req, Response $resp){
	$oldpass = $req->getParam('oldPassword');
	$newpass = $req->getParam('newPassword');
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	if(isset($oldpass) and isset($newpass) and isset($username) and isset($publicKey)){
		try{
			$dbn = new db();
			$key = ' FitSKchgoHOOKing666';
			$string = $key.'34iIlm'.$oldpass.'io9m-';
			$oldP = hash('sha256', $string);
			$string2 = $key.'34iIlm'.$newpass.'io9m-';
			$newP = hash('sha256', $string2);
			$mcrypto = new mcrypt();
			$newKey = $mcrypto->mCryptThis(time());
			$dbn = $dbn->connect();
			$sql = "update admin set password = :newP, publicKey = :newSessionKey where password = :password and username = :username and publicKey = :sessionKey";
			$f = $dbn->prepare($sql);
			$f->bindParam(':newP', $newP);
			$f->bindParam(':password', $oldP);
			$f->bindParam(':newSessionKey', $newKey);
			$f->bindParam(':username', $username);
			$f->bindParam(':sessionKey', $publicKey);
			$f->execute();
			$id = $f->rowCount();
			if(is_numeric($id) and $id > 0 ){
				$g = '{"error":{"message":"", "status":"0"},"success":{"message":"Password updated succesfully","status":"200"}, "content":{"publicKey":"'.$newKey.'", "username":"'.$username.'"}}';
			}else{
				$g = '{"error":{"message":"Sorry, Access Issues with '.$username.'. login again and try again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Sorry, All parameters are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/admin/login', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$mcrypto = new mcrypt();
	$lastseen = time();
	$publicKey = $mcrypto->mCryptThis(time());
	if(isset($username) and isset($password)){
		try{
			$dbn = new db();
			$dbn->startToday();
			$sql = "select count(*) from admin where username='".$username."' and password='".$encryptedPassword."'";
			$dbn = $dbn->connect();
			$qr = $dbn->query($sql);
			if ($qr->fetchColumn() > 0) {
				$sql = "update admin set publicKey = '".$publicKey."', lastseen = '".$lastseen."' where username = '".$username."'";
				$stmt = $dbn->prepare($sql);
			    $stmt->execute();
				$dbn = new db();
				$data = $dbn->selectFromQuery("select username, fullname, email, phone, lastseen, gender, address, canteenID from admin where username = '".$username."' and publicKey = '".$publicKey."'");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Login successful", "status":"200"}, "content":{"publicKey":"'.$publicKey.'", "username":"'.$username.'", "data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Login credentials match not found", "status":"1"}}';
			}
		}catch(PDOException $e){
			$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Login credentials not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/get/profile', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = "f";
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			$dbn->startToday();
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					$q = "SELECT username, fullname, email, phone, lastseen, address, gender FROM admin WHERE username != :username and publicKey != :key";
					$f = $db->prepare($q);
					$f->execute(array(':key' => $publicKey, ':username' => $username));
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"Search is required", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid. Sign in again. Sign in again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
    }else{
        $g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
    }
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

	/* Cards and friends */
	/*
	*	Everything Card Here Thats Admin can do
	*/
$app->post('/api/admin/sync/cardDetails', function(Request $req, Response $resp){
	$cardDetails = $req->getParam("cards");
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	if(isset($username) and isset($publicKey) and isset($cardDetails)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$cardDetails = urldecode($cardDetails);
			//die($cardDetails);
			$dbn = new db();
			if($dbn->isExist($q)){
				if($dbn->isJson($cardDetails)){
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[]);
					$cardDetails = json_decode($cardDetails);
					$datum = $cardDetails->data;
					$count = 0;
					$cardCount = count($datum);
					$date = time();
					while($count < $cardCount){
						if(isset($datum[$count]->cardSerial) and isset($datum[$count]->issuerID) and isset($datum[$count]->dateCreated)){
							$q = "INSERT INTO cards (cardSerial, issuerID, dateCreated, dateSynced, customer) SELECT * FROM (SELECT :cardSerial, :issuerID, :dateCreated, :dateSynced, :customer) AS tmp WHERE NOT EXISTS (SELECT cardSerial FROM cards WHERE cardSerial = :cardSerial or customer = :customer) LIMIT 1";
							$db = $dbn->connect();
							$f = $db->prepare($q);
							$datum[$count]->dateCreated = $datum[$count]->dateCreated/1000;
							$f->bindValue(":cardSerial", $datum[$count]->cardSerial);
							$f->bindValue(":issuerID", $datum[$count]->issuerID);
							$f->bindValue(":dateCreated", $datum[$count]->dateCreated);
							$f->bindValue(":customer", $datum[$count]->customer);
							$f->bindValue(":dateSynced", $date);
							$f->execute();
							if(is_numeric($db->lastInsertId()) and $db->lastInsertId() > 0){
								array_push($statuses["successful"], $datum[$count]->cardSerial);
							}else{
								array_push($statuses["duplicate"], $datum[$count]->cardSerial);
							}
						}else{
							isset($datum[$count]->cardSerial)? array_push($statuses["failed"],$datum[$count]->cardSerial) : array_push($statuses["failed"], "Nulus");
						}
						$count++;
					}
					$statuses = json_encode($statuses);
					$sql = "select * from cards";
					$data = $dbn->selectFromQuery($sql);
					$v = json_decode($data);
					$tcount = count($v);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"Sync Sync","code":"200"}, "content":{"totalCards":"'.$tcount.'", data":'.$statuses.'}}';					
				}else{
					$g = '{"error":{"message":"The cards is not a valid JSON. Array of cards is Expected", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->put('/api/admin/set/access/card/{to}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$to = $req->getAttribute('to');
	$cardSerial = $req->getParam('cardSerial');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(is_numeric($to) and ($to == 1 or $to == 0)){
					if(isset($cardSerial)){
						$sql = "UPDATE `cards` set valid = :valid where cardSerial = :cardSerial";
						$dbn = $dbn->connect();
						$f = $dbn->prepare($sql);
						$f->bindParam(':valid', $to);
						$f->bindParam(':cardSerial', $cardSerial);
						$f->execute();
						$sql = "select fullname, accountNumber, cards.cardSerial as cardSerial, profilePicture, accountBalance, otherBalance, dateCreated, dateSynced, valid from cards left join customers on cards.customer = customers.accountNumber";
						$f = new db();
						$data = $f->selectFromQuery($sql);
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Customer was successfully updated", "status":"200"}, "content":{"cardSerial" : "'.$cardSerial.'", "cards":'.$data.'}}';
					}else{
						$g = '{"error":{"message":"The Card specified have not been found", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"The new Status could not be set because it is invalid. O to lock Or 1 to unlock profile", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/getCards/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$type = $req->getAttribute("type");
	if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
		$username  = $headers['HTTP_USERNAME'][0];
		if(array_key_exists('HTTP_CARDSERIAL', $headers)){ $cardSerial = $headers['HTTP_CARDSERIAL'][0]; }
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				$sQ = "";
				$db = $dbn->connect();
				if(isset($type)){
					if($type == "-") $sQ = "";
					elseif($type == "unAssigned") $sQ = " where cards.assigned = 0";
					elseif($type == "assigned") $sQ = " where cards.assigned = 1";
					else $sQ = " where cards.customer = '$type'";
				}
				if(isset($cardSerial)){
					$clause = (strlen($sQ) > 1)? " and " : " where ";
					$sQ = $sQ.$clause." cards.cardSerial = '".$cardSerial."'";
				}
				$sql = "select fullname, accountNumber, cards.cardSerial as cardSerial, profilePicture, accountBalance, otherBalance, dateCreated, dateSynced, assigned, valid from cards left join customers on cards.customer = customers.accountNumber ".$sQ;
				$f = $db->prepare($sql);
				$f->execute();
				$row = $f->fetchAll();
				if($row){
					$data = json_encode($row, true);
				}else{
					$data = "[]";
				}
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});



	/*
	* Everything Devices Here The Admin Can do
	*/
$app->post('/api/admin/create/devices', function(Request $req, Response $resp){
	$deviceCode = $req->getParam("deviceCode");
	$deviceName = $req->getParam("deviceName");
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	if(isset($username) and isset($publicKey) and isset($deviceCode) and isset($deviceName) and strlen($deviceName) > 1  and strlen($deviceCode) > 1){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				$date = time();
				$q = "INSERT INTO devices (deviceCode, deviceName, dateCreated) SELECT * FROM (SELECT :devCode, :devName, :dateCreated) AS tmp WHERE NOT EXISTS (SELECT deviceCode FROM devices WHERE deviceCode = :devCode or deviceName = :devName) LIMIT 1";
				$db = $dbn->connect();
				$f = $db->prepare($q);
				$f->bindValue(":devCode", $deviceCode);
				$f->bindValue(":devName", $deviceName);
				$f->bindValue(":dateCreated", $date);
				$f->execute();
				$sql = "select*from devices";
				$data = $dbn->selectFromQuery($sql);
				if(is_numeric($db->lastInsertId()) and $db->lastInsertId() > 0){
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"Device Created","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"Device Created","code":"200"}, "content":{"data":'.$data.'}}';
				}
			}else{
				$g = '{"error":{"message":"Username and key does not match any admin", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/getDevices/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$type = $req->getAttribute("type");
	if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			$sQ = "";
			if($dbn->isExist($q)){
				$db = $dbn->connect();
				if(isset($type)){					
					if($type == "-") $sQ = "";
					if($type == "unAssigned") $sQ = " where devices.agentID is NULL";
					if($type == "assigned") $sQ = " where devices.agentID not NULL";
				}
				$sql = "SELECT devices.deviceCode, devices.deviceName, devices.dateCreated, devices.deviceID, devices.agentID, agent.fullname, agent.phone, agent.email, agent.totalCollected from devices left join agent on devices.agentID = agent.agentID ".$sQ;
				//$sql = "select devices.deviceCode, devices.deviceName, canteen.name, canteen.totalServed, canteen.unsettled, canteen.operator from devices left join canteen on devices.merchantID = canteen.merchantID".$sQ;
				$f = $db->prepare($sql);
				$f->execute();
				$row = $f->fetchAll();
				if($row){
					$data = json_encode($row, true);
				}else{
					$data = "[]";
				}
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

//Admin gives points
$app->post('/api/admin/create/points', function(Request $req, Response $resp){
	$pointAmount = $req->getParam('amount');
	$agentID = $req->getParam('agentID');
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$password = $req->getParam("k");
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	if(isset($username) and isset($publicKey) and isset($agentID) and isset($pointAmount)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey' and password = '$password'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(is_numeric($pointAmount) and $pointAmount > 0){
					$dat = time();
					$t = (time()%86400);
					$today = (time() - $t);
					$data = $dbn->selectFromQuery("select left as amount from limithistory where day = '$today'");
					if(count(json_decode($data)) > 0){
						$data = json_decode($data);
						$am = $data[0]->amount;
					}else{
						$am = 0;
					}
					if($am >= $pointAmount){
						$zs = "update limithistory set left = left - :amount where day = :today";
						$f = $dbn->connect();
						$f = $f->prepare($zs);
						$f->bindValue(":amount", $pointAmount);
						$f->bindValue(":today", $today);
						$f->execute();
						$a = "INSERT INTO `points` (agentID, transDate, admin, amount) VALUES (:agent, :transDate, :admin, :amount)";
						$f = $dbn->connect();
						$f = $f->prepare($a);
						$f->bindValue(":agent", $agentID);
						$f->bindValue(":transDate", $dat);
						$f->bindValue(":admin", $username);
						$f->bindValue(":amount", $pointAmount);
						$f->execute();
						$b = "update agent set totalVouchers = totalVouchers + :amount, voucherLeft = voucherLeft + :amount where agentID = :agentID";
						$f = $dbn->connect();
						$f = $f->prepare($b);
						$f->bindValue(":agentID", $agentID);
						$f->bindValue(":amount", $pointAmount);
						$f->execute();
						$data = $dbn->selectFromQuery("select points.agentID as agentID, points.transDate, points.admin as admin, points.amount, agent.fullname as fullname, agent.voucherLeft, agent.totalVouchers, agent.phone, agent.email from points left join agent on agent.agentID = points.agentID ");
						$hist = $dbn->selectFromQuery("select * from limithistory");
						$g = '{"error":{"message":"","status":"0"}, "success":{"message":"The Point was added succesfully","code":"200"}, "content":{"data":'.$data.', "hist":'.$hist.'}}';
					}else{
						$g = '{"error":{"message":"Not enough point available in system. Top up system and try again", "status":"1"}}';	
					}
				}else{
					$g = '{"error":{"message":"The Point must be all numeric greater than 0", "status":"1"}}';	
				}
			}else{
				$g = '{"error":{"message":"The credentials do not match any user", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
		->withHeader('Content-Type', 'application/json')
		//->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	 	->write($g);
});

$app->post('/api/admin/set/pointLimit', function(Request $req, Response $resp){
	$pointAmount = $req->getParam('amount');
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$password = $req->getParam("k");
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	if(isset($username) and isset($publicKey) and isset($pointAmount)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey' and password = '$password'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(is_numeric($pointAmount) and $pointAmount > 0){
					$dat = time();
					$a = "INSERT INTO `settings` (tlimit, whoDid, transDate) VALUES (:lim, :whodid, :transDate)";
					$f = $dbn->connect();
					$f = $f->prepare($a);
					$f->bindValue(":transDate", $dat);
					$f->bindValue(":whodid", $username);
					$f->bindValue(":lim", $pointAmount);
					$f->execute();
					$data = $dbn->selectFromQuery("select * from settings order by id desc");
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"The New Limit was set succesfully. Effective from Tomorrow","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"The Point must be all numeric greater than 0", "status":"1"}}';	
				}
			}else{
				$g = '{"error":{"message":"The credentials do not match any user", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
		->withHeader('Content-Type', 'application/json')
		//->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	 	->write($g);
});

$app->post('/api/admin/addto/pointLimit', function(Request $req, Response $resp){
	$pointAmount = $req->getParam('amount');
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$password = $req->getParam("k");
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	if(isset($username) and isset($publicKey) and isset($pointAmount)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey' and password = '$password'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(is_numeric($pointAmount) and $pointAmount > 0){
					$t = (time()%86400);
					$dat = (time() - $t);
					$a = "update limithistory set amount = amount + :amount, `left` = `left` + :amount where day = :today";
					$f = $dbn->connect();
					$f = $f->prepare($a);
					$f->bindValue(":today", $dat);
					$f->bindValue(":amount", $pointAmount);
					$f->execute();
					$data = $dbn->selectFromQuery("select * from limithistory where day = '$dat'");
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"The New Limit was set succesfully. Effective immediately","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"The Point must be all numeric greater than 0", "status":"1"}}';	
				}
			}else{
				$g = '{"error":{"message":"The credentials do not match any user", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
		->withHeader('Content-Type', 'application/json')
		//->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	 	->write($g);
});

$app->get('/api/admin/get/tlimit/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$type = $req->getAttribute("type");
	if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			$dbn->startToday();
			$sQ = "";
			if($dbn->isExist($q)){
				$db = $dbn->connect();
				$sql = "SELECT max(id), tlimit, whodid, transDate  from settings";
				$f = $db->prepare($sql);
				$f->execute();
				$row = $f->fetchAll();
				if($row){
					$data = json_encode($row, true);
				}else{
					$data = "[]";
				}
				$t = (time()%86400);
				$today = (time() - $t);
				$lim = $dbn->selectFromQuery("select * from limithistory where day = '$today'");
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.', "hist":'.$lim.'}}';
			}else{
				$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});


$app->get('/api/admin/getPoints/{agent}/{from}/{to}', function(Request $req, Response $resp){
	$search = $req->getAttribute("agent");
	$from = $req->getAttribute("from");
	$to = ($req->getAttribute("to") > time())? time() + 86399 : $req->getAttribute("to") + 86399;
	$headers = $req->getHeaders();
	//$type = $req->getAttribute("type");
	if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			$sQ = "";
			if($dbn->isExist($q)){
				$db = $dbn->connect();
				$sqi = "where (transDate >= :from and transDate <= :to)";
				$arr = array();
				$arr[":from"] = $from; $arr[":to"] = $to;
				if($search == "-"){}else{$sqi = $sqi." and agent.agentID = :agent"; $arr[":agent"] = $search; }
				$sql = "select points.agentID as agentID, points.transDate, points.admin as admin, points.amount, agent.fullname as fullname, agent.voucherLeft, agent.totalVouchers, agent.phone, agent.email from points left join agent on agent.agentID = points.agentID ".$sqi;
				$f = $db->prepare($sql);
				$f->execute($arr);
				$row = $f->fetchAll();
				if($row){
					$data = json_encode($row, true);
				}else{
					$data = "[]";
				}
				$hist = $dbn->selectFromQuery("select * from limithistory where (day >= '$from' and day <= '$to')");
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.', "hist":'.$hist.'}}';
			}else{
				$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});



/*
* Here is Everything About Agent As available to the Admin
*/
$app->post('/api/admin/create/agent', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$name = $req->getParam('fullname');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$gender = $req->getParam('gender');
	$address = $req->getParam('address');
	$device = $req->getParam('device');
	$password = $req->getParam('password');
	$pin = $req->getParam('pin');
	$agentUsername = $req->getParam('agentUsername');
	$merchantID = "Manna567";//$req->getParam('merchantID');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$mcrypto = new mcrypt();
	$agentID = $mcrypto->mCryptThis($username."/".time()/35);
	$added = time();
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(true){
					if(isset($name) and isset($phone) and isset($email) and isset($username) and isset($password) and isset($pin)){
						if(is_numeric($phone)){
							if(!$dbn->isExist("select*from agent where email = '$email'")){
								if(!$dbn->isExist("select*from agent where username = '$agentUsername'")){
									if(!$dbn->isExist("select*from agent where phone = '$phone'")){
										if($dbn->isExist("select*from devices where deviceCode = '$device' and agentID is NULL")){
											$dbn = $dbn->connect();
											$sql = "INSERT INTO agent (fullname, email, phone, password,  merchantID, username, addedBy, agentID, gender, device, address, pin) VALUES (:fullname, :email, :phone, :password, :merchant, :username, :added, :cashier, :gender, :device, :address, :pin)";
											$f = $dbn->prepare($sql);
											$f->bindParam(':fullname', $name);
											$f->bindParam(':email', $email);
											$f->bindParam(':phone', $phone);
											$f->bindParam(':password', $encryptedPassword);
											$f->bindParam(':merchant', $merchantID);
											$f->bindParam(':username', $agentUsername);
											$f->bindParam(':added', $added);
											$f->bindParam(':cashier', $agentID);
											$f->bindParam(':gender', $gender);
											$f->bindParam(':device', $device);
											$f->bindParam(':address', $address);
											$f->bindParam(':pin', $pin);
											$f->execute();
											$q2 = "update devices set merchantID = :merchant, agentID = :agentID where deviceCode = :device";
											$f = $dbn->prepare($q2);
											$f->bindParam(':merchant', $merchantID);
											$f->bindParam(':device', $device);
											$f->bindParam(':agentID', $agentID);
											$f->execute();
											$f = new db();
											$data = $f->selectFromQuery("SELECT fullname, email, pin, phone, merchantID, agentID, username, device, totalCollected FROM agent order by id desc");
											$data2 = $f->selectFromQuery("SELECT * FROM devices where agentID is NULL");
											$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Agent was successfully added", "status":"200"}, "content":{"agents":'.$data.', "devices":'.$data2.'}}';
										}else{
											$g = '{"error":{"message":"Sorry, the Device is not available", "status":"1"}}';
										}
									}else{
										$g = '{"error":{"message":"Sorry, the phone number exists", "status":"1"}}';		
									}
								}else{
									$g = '{"error":{"message":"Sorry, the username exists", "status":"1"}}';
								}
							}else{
								$g = '{"error":{"message":"Sorry, the email exists", "status":"1"}}';	
							}
						}else{
							$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"All the required parameters are not found", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"Merchant is invalid", "status":"1"}}';
				}				
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->put('/api/admin/edit/agent/{agentID}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$name = $req->getParam('fullname');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$address = $req->getParam('address');
	$password = $req->getParam('password');
	$newDevice = $req->getParam('newDevice');
	$oldDevice = $req->getParam('oldDevice');
	$cashID = $req->getAttribute('agentID');
	$pin = $req->getParam('pin');
	$newDevice = ($newDevice == "0")? NULL :  $newDevice;
	if(isset($password)){
		$p = ", password = :pass";
		$key = ' FitSKchgoHOOKing666';
		$string = $key.'34iIlm'.$password.'io9m-';
		$encryptedPassword = hash('sha256', $string);
	}else{ $p = "";}
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(true){//isset($merchantID) and $dbn->isExist("select*from canteen where merchantID = '$merchantID'")){
					if(isset($name) and isset($phone) and isset($email)){
						if(is_numeric($phone)){
							if(!$dbn->isExist("select*from agent where email = '$email' and agentID != '$cashID'")){
								if(!$dbn->isExist("select*from agent where phone = '$phone' and agentID != '$cashID'")){
									$dbn = $dbn->connect();
									$sql = "update agent set fullname = :fullname, pin = :pin, email = :email, phone = :phone, device = :newDevice, address = :address ".$p." where agentID = :agent";
									$f = $dbn->prepare($sql);
									$f->bindParam(':fullname', $name);
									$f->bindParam(':email', $email);
									$f->bindParam(':phone', $phone);
									$f->bindParam(':newDevice', $newDevice);								
									$f->bindParam(':address', $address);									
									$f->bindParam(':agent', $cashID);
									$f->bindParam(':pin', $pin);
									if(isset($password)){ $f->bindParam(':pass', $encryptedPassword); }
									$f->execute();
									$nsql = "update devices set agentID = NULL where deviceCode = :device";
									$nsql2 = "update devices set agentID = :agent where deviceCode = :device";
									$f = $dbn->prepare($nsql);
									$f->bindParam(':device', $oldDevice);
									$f->execute();
									$f = $dbn->prepare($nsql2);
									$f->bindParam(':device', $newDevice);
									$f->bindParam(':agent', $cashID);
									$f->execute();
									$f = new db();
									$data = $f->selectFromQuery("SELECT fullname, email, phone, merchantID, agentID, username, gender, device, address, totalCollected , totalVouchers, voucherLeft FROM agent where agentID = '$cashID'");
									$data2 = $f->selectFromQuery("SELECT * FROM devices where agentID is NULL");
									$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Agent Profile was successfully Updated", "status":"200"}, "content":{"data":'.$data.', "devices":'.$data2.'}}';
								}else{
									$g = '{"error":{"message":"Sorry, the phone number exists", "status":"1"}}';		
								}
							}else{
								$g = '{"error":{"message":"Sorry, the email exists", "status":"1"}}';	
							}
						}else{
							$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"All the required parameters are not found", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"Merchant is invalid", "status":"1"}}';
				}				
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->put('/api/admin/agent/changeDevice/{to}', function(Request $req, Response $resp){
	$newDevice = $req->getAttribute('to');
	$oldDevice = $req->getParam('oldDevice');
	$agentID = $req->getParam('agentID');
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$merchant = $req->getParam('merchantID');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($oldDevice) and isset($newDevice) and isset($agentID)){
					if($oldDevice != $newDevice){
						$dbn = $dbn->connect();
						$sql3 = "update agent set device = :device where agentID = :agent";
						$f = $dbn->prepare($sql3);
						$f->bindParam(':agent', $agentID);
						$f->bindParam(':device', $newDevice);
						$f->execute();
						$sql = "update devices set agentID = :cashier where deviceCode = :device";
						$sql2 = "update devices set agentID = :cashier where deviceCode = :device";
						$f = $dbn->prepare($sql);
						$f->bindParam(':cashier', $agentID);
						$f->bindParam(':device', $newDevice);
						$f->execute();
						$f = $dbn->prepare($sql2);
						$merchant = NULL;
						$agentID = NULL;
						$f->bindParam(':cashier', $agentID);
						$f->bindParam(':device', $oldDevice);
						$f->execute();
						$db = new db();
						$data = $db->selectFromQuery("SELECT fullname, email, phone, merchantID, agentID, username, devicetotalCollected FROM agent order by id desc");
						$data2 = $db->selectFromQuery("select * from devices");
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"cashier was successfully added", "status":"200"}, "content":{"data":'.$data.', "devices":'.$data2.'}}';
					}else{
						$g = '{"error":{"message":"Old Device and New Device cannot be the same", "status":"1"}}';	
					}
				}else{
					$g = '{"error":{"message":"All the required parameters are not found", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->put('/api/admin/set/access/agent/{to}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$to = $req->getAttribute('to');
	$cashier = $req->getParam('agentID');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(is_numeric($to) and $to == 1 or $to == 0){
					if(isset($cashier)){
						$sql = "UPDATE `cashier` set isValid = :valid where agentID = :cashID";
						$dbn = $dbn->connect();
						$f = $dbn->prepare($sql);
						$f->bindParam(':valid', $to);
						$f->bindParam(':cashID', $cashier);
						$f->execute();
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"cashier was successfully updated", "status":"200"}, "content":{"cashierID" : "'.$cashier.'"}}';
					}else{
						$g = '{"error":{"message":"The agentID have not been found", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"The new Status could not be set because it is invalid. O to lock Or 1 to unlock profile", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});


$app->put('/api/admin/post/transaction', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$to = $req->getParam('to');
	$transID = $req->getParam('transID');
	$account = $req->getParam('accountNumber');
	$amount = $req->getParam('amount');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(is_numeric($to) and $to == 1 or $to == 0){
					if(isset($transID)){
						$sql = "UPDATE `transactions` set settled = :settle where transID = :transID";
						$dbn = $dbn->connect();
						$f = $dbn->prepare($sql);
						$f->bindParam(':settle', $to);
						$f->bindParam(':transID', $transID);
						$f->execute();
						$sql = "update customers set accountBalance = accountBalance + :balance where accountNumber = :number";
						$f = $dbn->prepare($sql);
						$f->bindParam(':balance', $amount);
						$f->bindParam(':number', $account);
						$f->execute();
						$dbn = new db();
						$data = $dbn->selectFromQuery("SELECT transID, tmp.agentID as agentID, amount, syncDate, cardBalance, cardSerial, transDate, settled, accountNumber, tellerNumber, tmp.fullname as customerName, accountBalance, agent.fullname as agentName FROM (SELECT transID, agentID, amount, syncDate, cardBalance, transactions.cardSerial as cardSerial, transDate, settled, transactions.accountNumber as accountNumber, tellerNumber, fullname, accountBalance from transactions left join customers on transactions.accountNumber = customers.accountNumber) as tmp left join agent on agent.agentID = tmp.agentID");
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Transaction was successfully posted", "status":"200"}, "content":{"transactions" :'.$data.'}}';
					}else{
						$g = '{"error":{"message":"The transID have not been found", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"The new Status could not be set because it is invalid. O to lock Or 1 to unlock profile", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/get/agent/{agentID}', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute('agentID');
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					if($search == "-"){
						$q = "SELECT fullname, email, pin, phone, merchantID, agentID, username, device, address, totalCollected, totalVouchers, voucherLeft FROM agent order by id desc";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT fullname, email, phone, pin, merchantID, agentID, username, gender, device, address, totalCollected , totalVouchers, voucherLeft FROM agent where agentID = :name order by id desc";
						$f = $db->prepare($q);
						$f->execute(array(':name' => $search));
					}
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"Search is required", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid. Sign in again. Sign in again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
    }else{
        $g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
    }
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/admin/delete/agent/{agentID}', function (Request $req, Response $resp){
	$cashierID = $req->getAttribute('agentID');
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				$sql = "delete from agent where agentID = :cashier";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindParam(':cashier', $cashierID);
				$f->execute();
				$sql = "update devices set agentID = NULL , merchantID = NULL where agentID = :cashier";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindParam(':cashier', $cashierID);
				$f->execute();
				$data = $dbn->selectFromQuery("SELECT fullname, email, phone, pin, merchantID, agentID, username, device, totalCollected FROM agent order by id desc");
				$data2 = $f->selectFromQuery("SELECT * FROM devices where agentID is NULL");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Agent Purge successful", "status":"200"}, "content":{"data":'.$data.', "devices":'.$data2.'}}';
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid. Sign in again. Sign in again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});


/*
* This is the Customers and Fam.
* The everything of customers as seen by the Admin
*/

$app->post('/api/admin/create/customer', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$data = $req->getParam('customer');
	if(is_string($data)){
		$data = isset($data)? urldecode($data) : $data;
	}
	$data = is_array($data)? json_encode($data) : $data;
	$dataz = '[]';
	$dat = '[]';
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if($dbn->isJson($data)){
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[], "errorLogs"=>[]);
					$CustomerDetails = json_decode($data);
					$datum = $CustomerDetails->data;
					$count = 0;
					$CustomerCount = count($datum);
					$date = time();
					while($count < $CustomerCount){
						$name = $datum[$count]->name;
						$accountNumber = $datum[$count]->accountNumber;
						$cardSerial = $datum[$count]->cardSerial;
						$address = $datum[$count]->address;
						$gender = $datum[$count]->gender;
						$phoneNumber = $datum[$count]->phone;
						$email = $datum[$count]->email;
						$balance = $datum[$count]->balance;
						$status = 1;
						if(isset($name) and isset($accountNumber) and isset($cardSerial) and isset($address)){
							if(is_numeric($accountNumber)){
								if(!$dbn->isExist("select*from customers where accountNumber = '$accountNumber' or cardSerial = '$cardSerial'")){
									if(!$dbn->isExist("select*from cards where cardSerial = '$cardSerial'")){
										try{
											$q = "insert into customers (fullname, accountNumber, cardSerial, address, gender, status, phone, email, accountBalance) values (:name, :accountNumber, :cardSerial, :address, :gender, :status, :phone, :email, :balance)";
											$dbn = new db();
											$dbn = $dbn->connect();
											$f = $dbn->prepare($q);
											$f->bindParam(':name', $name);
											$f->bindParam(':accountNumber', $accountNumber);
											$f->bindParam(':cardSerial', $cardSerial);
											$f->bindParam(':address', $address);
											$f->bindParam(':gender', $gender);
											$f->bindParam(':status', $status);
											$f->bindParam(':phone', $phoneNumber);
											$f->bindParam(':email', $email);
											$f->bindParam(':balance', $balance);
											$f->execute();
											$q = "update cards set assigned = 1 where cardSerial = :card";
											$f = $dbn->prepare($q);
											$f->bindParam(':card', $cardSerial);
											$f->execute();
											$f = new db();
											array_push($statuses["successful"], $datum[$count]->accountNumber);
										}catch(PDOException $ex){
											array_push($statuses["duplicate"], $datum[$count]->accountNumber);
										}
										$f = new db();
										$dat = $f->selectFromQuery("SELECT * FROM customers");
										$dataz = $f->selectFromQuery("SELECT * FROM cards where assigned is NULL or assigned = 0");
									}else{
										array_push($statuses["failed"], $datum[$count]->accountNumber);
										array_push($statuses["errorLogs"], "The Card is invalid" );
										$g = '{"error":{"message":"The Card is invalid", "status":"1"}}';
									}
								}else{
									array_push($statuses["failed"], $datum[$count]->accountNumber);
									array_push($statuses["errorLogs"], "The Account Number Exists" );
									$g = '{"error":{"message":"The Account Number Exists", "status":"1"}}';
								}
							}else{
								array_push($statuses["failed"], $datum[$count]->accountNumber);
								array_push($statuses["errorLogs"], "The account Number is invalid" );
								$g = '{"error":{"message":"The account Number is invalid", "status":"1"}}';
							}
						}else{
							array_push($statuses["failed"], $datum[$count]->accountNumber);
							array_push($statuses["errorLogs"], "All The required fields are not correctly filled out" );
							$g = '{"error":{"message":"All The required fields are not correctly filled out", "status":"1"}}';
						}
						$count++;
					}
					$statuses = json_encode($statuses);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"The New Customer was created","code":"200"}, "content":{"data":'.$statuses.', "cards":'.$dataz.', "Customers":'.$dat.'}}';
				}else{
					$g = '{"error":{"message":"The Customers is not a valid JSON. Array of Customers is Expected", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});



$app->put('/api/admin/update/customer', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$accountNumber = $req->getParam('accountNumber');
	$cardSerial = $req->getParam('cardSerial');
	$newCardSerial = $req->getParam('newCardSerial');
	$newCardSerial = isset($newCardSerial)? $newCardSerial : $cardSerial;
	$address = $req->getParam('address');
	$gender = $req->getParam('gender');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$accountBalance = $req->getParam('balance');
	$status = 1;//$req->getParam('status');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($accountNumber) and isset($cardSerial)){
					if(is_numeric($accountNumber)){
						if(true){
							if(true){
								$q = "update customers set fullname = :name, cardSerial = :cardSerial, accountBalance = :accountBalance, phone = :phone, email = :email, gender = :gender, address = :address, status = :status where accountNumber = :accountNumber";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':cardSerial', $newCardSerial);
								$f->bindParam(':phone', $phone);
								$f->bindParam(':email', $email);
								$f->bindParam(':gender', $gender);
								$f->bindParam(':address', $address);								
								$f->bindParam(':status', $status);
								$f->bindParam(':accountNumber', $accountNumber);
								$f->bindParam(':accountBalance', $accountBalance);
								$f->execute();
								$q = "update cards set assigned = 1, valid = 1 where cardSerial = :card";
								$f = $dbn->prepare($q);
								$f->bindParam(':card', $cardSerial);
								$f->execute();
								if(isset($newCardSerial) and $newCardSerial != $cardSerial){
									$sq1 = "update cards set assigned = 0, valid = 0 where cardSerial = :card";
									$sq2 = "update cards set assigned = 1, valid = 1 where cardSerial = :card";
									$f = $dbn->prepare($sq1);
									$f->bindParam(':card', $cardSerial);
									$f->execute();
									$f = $dbn->prepare($sq2);
									$f->bindParam(':card', $newCardSerial);
									$f->execute();
								}
								$f = new db();
								$data = $f->selectFromQuery("SELECT fullname, accountBalance, accountNumber, phone, address, gender, email, customers.cardSerial as cardSerial, cards.valid as cardValid  FROM customers left join cards on cards.cardserial = customers.cardSerial WHERE customers.accountNumber = '$accountNumber'");
								$cards = $f->selectFromQuery("SELECT * FROM cards where assigned = 0");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Customer update successful", "status":"200"}, "content":{"data":'.$data.', "cards":'.$cards.'}}';
							}else{
								$g = '{"error":{"message":"Discount, goldClubDiscount, othersDiscount must be numeric", "status":"1"}}';
							}
						}else{
							$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"All the required parameters are not found", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}

		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->put('/api/admin/delete/customer/{accountNumber}', function (Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$cardSerial = $req->getParam('cardSerial');
	$accountNumber = $req->getAttribute('accountNumber');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				$sql = "delete from customers where accountNumber = :accountNumber";
				$dbn = $dbn->connect();
				$f = $dbn->prepare($sql);
				$f->bindParam(':accountNumber', $accountNumber);
				$f->execute();
				$sql = "update cards set assigned = 0 where cardSerial = :card";
				$f = $dbn->prepare($sql);
				$f->bindParam(':card', $cardSerial);
				$f->execute();
				$dbn = new db();
				$data = $dbn->selectFromQuery("SELECT*FROM customers");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Action on Customer successful", "status":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Validation Errors. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/get/customers/{searchParam}', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute('searchParam');
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					if($search == "-"){
						$q = "SELECT * FROM customers";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT fullname, accountBalance, accountNumber, phone, address, gender, email, customers.cardSerial as cardSerial, cards.valid as cardValid  FROM customers left join cards on cards.cardserial = customers.cardSerial WHERE customers.fullname  LIKE :name or customers.accountNumber = :account";
						$f = $db->prepare($q);
						$f->execute(array(':name' => '%'.$search.'%', ':account' => $search));
					}
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"Search is required", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid. Sign in again. Sign in again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
    }else{
        $g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
    }
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});



/*
* This is the transactions and Fam.
* The everything of transactions as seen by the Admin
*/
$app->get('/api/admin/get/transactions/{user}/{agent}/{from}/{to}/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute("agent");
	$from = $req->getAttribute("from");
	$to = ($req->getAttribute("to") > time())? time() + 86399 : $req->getAttribute("to") + 86399;
	$user = $req->getAttribute("user");
	$type = $req->getAttribute("type");
    if(array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search) and isset($to) and isset($from)){
					$db = $dbn->connect();
					$binds = [];
					if($search == "-") { $ssq = "";} else { $ssq = "where agentID = :agent"; $binds[':agent'] = $search; }
					if($ssq == "") $ssq = "where transDate >= :from and transDate <= :to"; else $ssq = $ssq." and (transDate >= :from and transDate <= :to )";
					if($user == "-"){}else{ $ssq = $ssq." and transactions.accountNumber = :account"; $binds[":account"] = $user; }
					if($type != "-"){ $ssq = $ssq." and customers.settled = :settled"; $binds[":settled"] = $type; }
					$q = "SELECT transID, tmp.agentID as agentID, amount, syncDate, cardBalance, cardSerial, transDate, settled, accountNumber, tellerNumber, tmp.fullname as customerName, accountBalance, agent.fullname as agentName FROM (SELECT transID, agentID, amount, syncDate, cardBalance, transactions.cardSerial as cardSerial, transDate, settled, transactions.accountNumber as accountNumber, tellerNumber, fullname, accountBalance from transactions left join customers on transactions.accountNumber = customers.accountNumber ".$ssq.") as tmp left join agent on agent.agentID = tmp.agentID";
					$f = $db->prepare($q);
					$binds[':from'] = $from; $binds[':to'] = $to;
					if($ssq == "") $f->execute(); else $f->execute($binds);
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"Search is required", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid. Sign in again. Sign in again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
    }else{
        $g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
    }
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});


/*
* This is the agent's Area
* This place typically defines the agent's action
*/
$app->post('/api/agent/login', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$deviceCode = $req->getParam('deviceCode');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	if(isset($username) and isset ($password) and isset($deviceCode)){
		$sql = "SELECT fullname, agent.pin as pin, agent.email, agent.phone, agent.agentID, agent.username, device, agent.totalCollected, agent.publicKey, agent.voucherLeft from agent where agent.username = :username and agent.password = :pass and agent.device = :device";
		try{
			$dbn = new db();
			$dbn = $dbn->connect();
			$f = $dbn->prepare($sql);
			$f->execute(array(':pass' => $password, ':username' => $username, ':device' => $deviceCode));
			$row = $f->fetch();
			if($row){
				$mcrypto = new mcrypt();
				$fkey = $mcrypto->mCryptThis(time()*rand());
				$row["publicKey"] = $fkey;
				$data = json_encode($row);
				$isdate = time();
				$sql2 = "update agent set publicKey = :fkey, lastSeen = :last where username = :user and password = :pass";
				$dbn = new db();
				$db = $dbn->connect();
				$f = $db->prepare($sql2);
				$f->bindParam(':fkey', $fkey);
				$f->bindParam(':last', $isdate);
				$f->bindParam(':user', $username);
				$f->bindParam(':pass', $password);
				$f->execute();
				$db = new db();
				//$data = substr($data,1,strlen($data)-2);
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Username and password does not match any Agent", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});


$app->post('/api/alter/password/agent', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('oldPassword');
	$password2 = $req->getParam('newPassword');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	$string = $key.'34iIlm'.$password2.'io9m-';
	$password2 = hash('sha256', $string);
	if(isset($username) and isset ($password)){
		$sql = "SELECT fullname, agent.email, agent.pin as pin, agent.phone, agent.agentID, agent.username agent.totalCollected, agent.publicKey from agent where agent.username = :username and agent.password = :pass";
		try{
			$dbn = new db();
			$dbn = $dbn->connect();
			$f = $dbn->prepare($sql);
			$f->execute(array(':pass' => $password, ':username' => $username));
			$row = $f->fetch();
			if($row){
				$mcrypto = new mcrypt();
				$fkey = $mcrypto->mCryptThis(time()*rand());
				$row["publicKey"] = $fkey;
				$data = json_encode($row);
				$isdate = time();
				$sql2 = "update agent set password = :password2, publicKey = :fkey, lastSeen = :last where username = :user and password = :pass";
				$dbn = new db();
				$db = $dbn->connect();
				$f = $db->prepare($sql2);
				$f->bindParam(':fkey', $fkey);
				$f->bindParam(':last', $isdate);
				$f->bindParam(':user', $username);
				$f->bindParam(':pass', $password);
				$f->bindParam(':password2', $password2);
				$f->execute();
				$db = new db();
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"Password Changed","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Username and password does not match any Cashier", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/agent/getCards/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute("type");
    if(array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
		$publicKey  = $headers['HTTP_PUBLICKEY'][0];
		$username  = $headers['HTTP_USERNAME'][0];
		if(isset($username) and isset($publicKey)){
			$q = "SELECT*FROM agent WHERE username = '$username' and publicKey = '$publicKey'";
			try{
				$dbn = new db();
				if($dbn->isExist($q)){
					if($type == 'valid'){
						$data2 = $dbn->selectFromQuery("select cardSerial from cards where valid = 1 and assigned = 1");
					}elseif($type == 'blocked'){
						$data2 = $dbn->selectFromQuery("select cardSerial from cards where valid = 0 and assigned = 1");
					}elseif($type == '-' or $type == 'all'){
						$data2 = $dbn->selectFromQuery("select cardSerial from cards where assigned = 1");
					}else{
						$data2 = '[]';
					}
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"cards":'.$data2.'}}';
				}else{
					$g = '{"error":{"message":"Supplied Credentials are invalid. Sign in again", "status":"1"}}';
				}
			}catch(PDOException $e){
				$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/agent/sync/transactions', function(Request $req, Response $resp){
	$json = $req->getParsedBody();
	$json = isset($json)? $json : $req->getBody();
	$headers = $req->getHeaders();
	//var_dump($headers);
	//$data = json_decode($json, true);
	/*$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$device = $req->getParam('deviceCode');
	$transactions = $req->getParam('transactions');	*/
	try{
		$json = (urldecode($json) != null)? urldecode($json) : $json;
	}catch(Exception $e){

	}
	if(is_string($json)){
		$json = isset($json)? urldecode($json) : $json;
		$c = new db();
		$json = $c->cleanString($json);
		//$json = trim(substr($json,1,strlen($json) -2));
		$json = json_decode($json);
	}else{
		$json = json_encode($json);
		$json = json_decode($json);
	}
	$date = time();
	$username = $json->agentID;
	$key = $json->publicKey;
	$device = $json->deviceCode;
	$transactions = $json->transactions;
	if(isset($username) and isset($key)){
		$sql = "SELECT*FROM agent where agentID = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				//$transactions = urldecode($transactions);
				if(true){//$dbn->isJson($transactions)){
					$imDB = new db();
					$imDB = $imDB->connect();
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[], "Internal-Failure"=>[]);
					//$transactions = json_decode($transactions);
					$transCount = count($transactions->data);
					$datum = $transactions->data;
					$f = 0;
					while($f < $transCount){
						if(isset($datum[$f]->cardSerial) and strlen($datum[$f]->cardSerial) > 0 and isset($datum[$f]->transDate) and strlen($datum[$f]->transDate) > 0 and isset($datum[$f]->transID) and strlen($datum[$f]->transID) > 0 and isset($datum[$f]->amount) and isset($datum[$f]->cardBalance) and isset($datum[$f]->tellerNumber)){							
							$a = "select * from cards where cardSerial = '".$datum[$f]->cardSerial."' and assigned = 1";
							if($dbn->isExist($a)){
								$datum[$f]->amount = (int)$datum[$f]->amount;
								$sql = "SELECT * FROM transactions where transID = '".$datum[$f]->transID."' and cardSerial = '".$datum[$f]->cardSerial."'";
								if(!$dbn->isExist($sql)){
									$g = "select accountNumber from customers where cardSerial = '".$datum[$f]->cardSerial."'";
									$x = $dbn->selectFromQuery($g);
									$data = json_decode($x,true);
									$account = $data[0]["accountNumber"];
									$b = "update customers set otherBalance = otherBalance + :newBalance where cardSerial = :cardSerial";
									$fa = $imDB->prepare($b);
									$fa->bindValue(':newBalance', $datum[$f]->amount);
									$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
									$fa->execute();									
									$u2 = "update agent set totalCollected = totalCollected + :collected, voucherLeft = voucherLeft - :collected where publicKey = :key and agentID = :agent";
									$fa = $imDB->prepare($u2);
									$fa->bindValue(':collected', $datum[$f]->amount);
									$fa->bindValue(':agent', $username);
									$fa->bindValue(':key', $key);
									$fa->execute();
									$u2 = "INSERT INTO transactions (agentID, amount, syncDate, cardBalance, cardSerial, transDate, transID, accountNumber, tellerNumber) VALUES (:agentID, :amount, :syncDate, :cardBalance, :cardSerial, :transDate, :transID, :accountNumber, :tellerNumber)";
									$syncDate = time();									
									$fa = $imDB->prepare($u2);
									$fa->bindValue(':agentID', $username);
									$fa->bindValue(':amount', $datum[$f]->amount);
									$fa->bindValue(':syncDate', $syncDate);
									$fa->bindValue(':cardBalance', $datum[$f]->cardBalance);
									$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
									$fa->bindValue(':transDate', round(floatval($datum[$f]->transDate)/1000));
									$fa->bindValue(':transID', $datum[$f]->transID);
									$fa->bindValue(':accountNumber', $account);
									$fa->bindValue(':tellerNumber', $datum[$f]->tellerNumber);
									$fa->execute();
									//die("yey!");
									array_push($statuses["successful"], $datum[$f]->transID);
								}else{
									array_push($statuses["duplicate"], $datum[$f]->transID);
								}
							}else{
								isset($datum[$f]->transID)? array_push($statuses["failed"],$datum[$f]->transID) : array_push($statuses["failed"], "Nulus");
							}
						}else{
							isset($datum[$f]->transID)? array_push($statuses["failed"],$datum[$f]->transID) : array_push($statuses["failed"], "Nulus");
							//die('Died here');
						}
						$f++;
					}
					$statuses = json_encode($statuses);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$statuses.'}}';
				}else{
					$g = '{"error":{"message":"The transaction is not a valid JSON. Array of transactions is Expected", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Invalid Credentials. Login and try again", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"agent username, publicKey are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/agent/getPoint', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
    if(array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
		$publicKey  = $headers['HTTP_PUBLICKEY'][0];
		$username  = $headers['HTTP_USERNAME'][0];
		if(isset($username) and isset($publicKey)){
			$q = "SELECT voucherLeft, totalCollected, pin FROM agent WHERE username = '$username' and publicKey = '$publicKey'";
			try{
				$dbn = new db();
				if($dbn->isExist($q)){
					$data = $dbn->selectFromQuery($q);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"agent username, publicKey match not found", "status":"1"}}';
				}
			}catch(PDOException $e){
				$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"agent username, publicKey are required", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"agent username, publicKey are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
		->withHeader('Content-Type', 'application/json')
		//->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	 	->write($g);
});

$app->get('/api/agent/getPin', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
    if(array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
		$publicKey  = $headers['HTTP_PUBLICKEY'][0];
		$username  = $headers['HTTP_USERNAME'][0];
		if(isset($username) and isset($publicKey)){
			$q = "SELECT pin FROM agent WHERE username = '$username' and publicKey = '$publicKey'";
			try{
				$dbn = new db();
				if($dbn->isExist($q)){
					$data = $dbn->selectFromQuery($q);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"agent username, publicKey match not found", "status":"1"}}';
				}
			}catch(PDOException $e){
				$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"agent username, publicKey are required", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"agent username, publicKey are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
		->withHeader('Content-Type', 'application/json')
		//->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	 	->write($g);
});

$app->post('/api/agent/create/customer', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$device = $req->getParam('deviceCode');
	$data = $req->getParam('customer');
	if(is_string($data)){
		$data = isset($data)? urldecode($data) : $data;
	}
	$data = is_array($data)? json_encode($data) : $data;
	$dataz = '[]';
	$dat = '[]';
	if(isset($username) and isset($key) and isset($device)){
		$sql = "select*from agent where username = '$username' and publicKey = '$key' and device = '$device'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if($dbn->isJson($data)){
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[], "errorLogs"=>[]);
					$CustomerDetails = json_decode($data);
					$datum = $CustomerDetails->data;
					$count = 0;
					$CustomerCount = count($datum);
					$date = time();
					while($count < $CustomerCount){
						$name = $datum[$count]->name;
						$accountNumber = $datum[$count]->accountNumber;
						$cardSerial = $datum[$count]->cardSerial;
						$address = $datum[$count]->address;
						$gender = $datum[$count]->gender;
						$phoneNumber = $datum[$count]->phone;
						$email = $datum[$count]->email;
						$balance = (isset($datum[$count]->balance) and is_numeric($datum[$count]->balance)) ? $datum[$count]->balance : 0;
						$status = 1;
						if(isset($name) and isset($accountNumber) and isset($cardSerial) and isset($phoneNumber)){
							if(is_numeric($accountNumber)){
								if(!$dbn->isExist("select*from customers where accountNumber = '$accountNumber' or cardSerial = '$cardSerial'")){
									if($dbn->isExist("select*from cards where cardSerial = '$cardSerial' and customer is NULL")){
										try{
											$q = "insert into customers (fullname, accountNumber, cardSerial, address, gender, status, phone, email, accountBalance) values (:name, :accountNumber, :cardSerial, :address, :gender, :status, :phone, :email, :balance)";
											$dbn = new db();
											$dbn = $dbn->connect();
											$f = $dbn->prepare($q);
											$f->bindParam(':name', $name);
											$f->bindParam(':accountNumber', $accountNumber);
											$f->bindParam(':cardSerial', $cardSerial);
											$f->bindParam(':address', $address);
											$f->bindParam(':gender', $gender);
											$f->bindParam(':status', $status);
											$f->bindParam(':phone', $phoneNumber);
											$f->bindParam(':email', $email);
											$f->bindParam(':balance', $balance);
											$f->execute();
											$q = "update cards set assigned = 1 where cardSerial = :card";
											$f = $dbn->prepare($q);
											$f->bindParam(':card', $cardSerial);
											$f->execute();
											$f = new db();
											array_push($statuses["successful"], $datum[$count]->accountNumber);
										}catch(PDOException $ex){
											array_push($statuses["duplicate"], $datum[$count]->accountNumber);
										}
										$f = new db();
										$dataz = $f->selectFromQuery("SELECT * FROM cards where valid = 0 and assigned = 1");
									}else{
										array_push($statuses["failed"], $datum[$count]->accountNumber);
										array_push($statuses["errorLogs"], "The Card is ivalid!" );
										$g = '{"error":{"message":"The Card is ivalid", "status":"1"}}';
									}
								}else{
									array_push($statuses["failed"], $datum[$count]->accountNumber);
									array_push($statuses["errorLogs"], "The Account Number or Card Exists" );
									$g = '{"error":{"message":"The Account Number Exists", "status":"1"}}';
								}
							}else{
								array_push($statuses["failed"], $datum[$count]->accountNumber);
								array_push($statuses["errorLogs"], "The account Number is invalid" );
								$g = '{"error":{"message":"The account Number is invalid", "status":"1"}}';
							}
						}else{
							array_push($statuses["failed"], $datum[$count]->accountNumber);
							array_push($statuses["errorLogs"], "All The required fields are not correctly filled out" );
							$g = '{"error":{"message":"All The required fields are not correctly filled out", "status":"1"}}';
						}
						$count++;
					}
					$statuses = json_encode($statuses);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"The New Customer was created","code":"200"}, "content":{"data":'.$statuses.'}}';
				}else{
					$g = '{"error":{"message":"The Customers is not a valid JSON. Array of Customers is Expected", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Errors. Agent profile not found", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/agent/getBalance/{cardSerial}', function(Request $req, Response $resp){
	$cardSerial = $req->getAttribute('cardSerial');
	$agentID = $req->getParam('agentID');
	$publicKey = $req->getParam('publicKey');
	if(isset($agentID) and isset($publicKey) and isset($cardSerial)){
		$dbn = new db();
		$sql = "select*from agent where agentID = '$agentID' and publicKey = '$publicKey'";
		try{
			if($dbn->isExist($sql)){
				$x = "select accountBalance from customers where cardSerial = '$cardSerial'";
				$data = $dbn->selectFromQuery($q);
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Validation Errors. Agent profile not found", "status":"1"}}';
			}
		}catch(PDOException $e){
			$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
			->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
			->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
			->write($g);
});
$app->post('/api/user/retrieve/password/{stage}', function(Request $req, Response $resp){
	$stage = $req->getAttribute('stage');
	if($stage == 1){
		$username = $req->getParam('username');
		$email = $req->getParam('email');
		if(isset($username) and isset($email)){
			$q = "select * from users where username = :username and email = :email";
			try{
				$db = new db();
				$db = $db->connect();
				$f = $db->prepare($q);
				$f->execute(array(':username' => $username, ':email'=>$email));
				$row = $f->fetch();
				if($row){
					$data = json_encode($row, true);
					$data = json_decode($data, true);
					$userID = $data[0];
					$v = time()*rand(1000,90000);
					$mcrypto = new mcrypt();
					$publicKey = $mcrypto->mCryptThis($v);
					$expiry = time() + 20000;
					$anq = "insert into resets (email, UID, resetkey, expiry) values (:email, :UId, :rkey, :expiry)";
					$db = new db();
					$db = $db->connect();
					$f = $db->prepare($anq);
					$f->bindValue(':email',$email);
					$f->bindValue(':UId',$userID);
					$f->bindValue(':rkey',$publicKey);
					$f->bindValue(':expiry',$expiry);
					$f->execute();
					$header = "Password Reset Request";
					$emailBody = "Your reset link is http://www.beepx.kunsana.com/reset?rt=".$publicKey."<br/> follow the link to reset your password.";
					$g = '{"error":{"message":"","status":""}, "success":{"message":"Email has been sent your registered email address. follow the link to reset your account","code":"200"}}';
				}else{
					$g = '{"error":{"message":"The account have not been found", "status":"1"}}';
				}
			}catch(PDOException $t){
				$g = '{"error":{"message":"Servive is unavailable", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"All Parameters are required", "status":"1"}}';
		}
	}else{
		$email= $req->getParam('email');
		$pkey = $req->getParam('pkey');
		$newPass = $req->getParam('npassword');
		if(isset($email) and isset($pkey)){
			$rtQ = "select*from resets where email = :em and resetkey = :k";
			if(strlen($newPass) > 6){
				try{
					$db = new db();
					$db = $db->connect();
					$f = $db->prepare($rtQ);
					$f->execute(array(':em' => $email, ':k'=>$pkey));
					$row = $f->fetch();
					if($row){
						$data = json_encode($row, true);
						$data = json_decode($data, true);
						$uid = $row[2];
						$key = ' FitSKchgoHOOKing666';
						$string = $key.'34iIlm'.$newPass.'io9m-';
						$encryptedPassword = hash('sha256', $string);
						$upQ = "update users set password = :np, publicKey = :pkl  where id = :id";
						$j = $db->prepare($upQ);
						$j->execute(array(':np'=>$encryptedPassword, ':pkl'=>$pkey, ':id'=>$uid));
						$g = '{"error":{"message":"","status":""}, "success":{"message":"Your password was successfully reset","code":"200"}}';
					}else{
						$g = '{"error":{"message":"The Reset profile have not been found", "status":"1"}}';
					}
				}catch(PDOException $pex){
					$g = '{"error":{"message":"Servive is unavailable", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Invalid Password", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"All Parameters are required", "status":"1"}}';
		}
	}
	return $resp->withStatus(200)
       		->withHeader('Content-Type', 'application/json')
      		->write($g);
});
?>