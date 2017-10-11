<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;
//error_reporting(0);
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
	$assignedCanteen = $req->getParam('canteenID');
	$assignedCanteen = (isset($assignedCanteen) and $assignedCanteen != 0)? $assignedCanteen : NULL;
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
								$sql = "insert into admin (fullname, email, phone, password, username, publicKey, gender, address, canteenID) values (:fullname, :email, :phone, :passwod, :username, :key, :gender, :address, :canteenID)";
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
								$f->bindValue(':canteenID', $assignedCanteen);
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
			$dbn->reverseCards();
			$sql = "select count(*) from admin where username='".$username."' and password='".$encryptedPassword."'";
			$dbn = $dbn->connect();
			$qr = $dbn->query($sql);
			if ($qr->fetchColumn() > 0) {
				$sql = "update admin set publicKey = '".$publicKey."', lastseen = '".$lastseen."' where username = '".$username."'";
				$stmt = $dbn->prepare($sql);
			    $stmt->execute();
				$dbn = new db();
				$data = $dbn->selectFromQuery("select username, fullname, email, phone, lastseen, gender, address, canteenID from admin where username = '".$username."' and publicKey = '".$publicKey."'");
				$cards = $dbn->selectFromQuery("select*from cards");
				$userType = $dbn->selectFromQuery("select*from userTypes");
				$staffs = $dbn->selectFromQuery("select*from staffs");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Login successful", "status":"200"}, "content":{"publicKey":"'.$publicKey.'", "username":"'.$username.'", "data":'.$data.', "userTypes":'.$userType.', "cards":'.$cards.', "staffs":'.$staffs.'}}';
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

$app->post('/api/admin/create/canteen', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$address = $req->getParam('address');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$bankName = $req->getParam('bankName');
	$accountName = $req->getParam('accountName');
	$accountNumber = $req->getParam('accountNumber');
	$operator = $req->getParam('canteenOperator');
	$deviceCode = $req->getParam('deviceCode');
	$mcrypto = new mcrypt();
	$merchantID = $mcrypto->mCryptThis(time()/35);
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($address) and isset($phone) and isset($email) and isset($deviceCode)){
					if(is_numeric($phone)){
						if(true){
							if(true){
								$q = "insert into canteen (name, address, accountNumber, phone, bankName, accountName, merchantID, email, operator) values (:name, :address, :accountnum, :phone, :bank, :accountname, :merchantID, :email, :operator)";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':address', $address);
								$f->bindParam(':accountnum', $accountNumber);
								$f->bindParam(':phone', $phone);
								$f->bindParam(':bank', $bankName);
								$f->bindParam(':accountname', $accountName);
								$f->bindParam(':merchantID', $merchantID);
								$f->bindParam(':email', $email);
								$f->bindParam(':operator', $operator);
								$f->execute();
								$f = new db();
								$data = $f->selectFromQuery("SELECT * FROM canteen");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Canteen creation successful", "status":"200"}, "content":{"merchantID":"'.$merchantID.'", "Name":"'.$name.'", "data":'.$data.'}}';
							}else{
								$g = '{"error":{"message":"Discount, goldClubDiscount, othersDiscount must be numeric", "status":"1"}}';
							}
						}else{
							$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
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

$app->post('/api/admin/update/canteen', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$address = $req->getParam('address');
	$phone = $req->getParam('phone');
	$bankName = $req->getParam('bankName');
	$accountName = $req->getParam('accountName');
	$accountNumber = $req->getParam('accountNumber');
	$email = $req->getParam('email');
	$merchantID = $req->getParam('merchantID');
	$deviceCode = $req->getParam('deviceCode');
	$newDeviceCode = $req->getParam('newDeviceCode');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($address) and isset($phone) and isset($email) and isset($deviceCode)){
					if(is_numeric($phone)){
						if(true){
							if(true){
								$q = "update canteen set name = :name, address = :address, accountNumber = :accountnum, phone = :phone, email = :email, bankName = :bank, accountName = :accountname where merchantID = :merchantID";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':address', $address);
								$f->bindParam(':accountnum', $accountNumber);
								$f->bindParam(':phone', $phone);
								$f->bindParam(':bank', $bankName);
								$f->bindParam(':accountname', $accountName);
								$f->bindParam(':merchantID', $merchantID);
								$f->bindParam(':email', $email);
								$f->bindParam(':operator', $operator);
								$f->execute();
								if(isset($newDeviceCode) and isset($deviceCode) and ($deviceCode != $newDeviceCode)){
									$sq = "update devices set merchantID = '' where merchantID = :merchant";
									$sq2 = "update devices set merchantID = :merchant where deviceCode = :device";
									$f = $dbn->prepare($sq);
									$f->bindParam(':merchant', $merchantID);
									$f->execute();
									$f = $dbn->prepare($sq2);
									$f->bindParam(':merchant', $merchantID);
									$f->bindParam(':device', $newDeviceCode);
									$f->execute();
								}
								$f = new db();
								$data = $f->selectFromQuery("SELECT * FROM canteen");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Canteen creation successful", "status":"200"}, "content":{"merchantID":"'.$merchantID.'", "Name":"'.$name.'", "data":'.$data.'}}';
							}else{
								$g = '{"error":{"message":"Discount, goldClubDiscount, othersDiscount must be numeric", "status":"1"}}';
							}
						}else{
							$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
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

$app->post('/api/admin/create/canteenCashier', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$name = $req->getParam('fullname');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$gender = $req->getParam('gender');
	$device = $req->getParam('device');
	$merchantID = $req->getParam('merchantID');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$phone.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$mcrypto = new mcrypt();
	$cashID = $mcrypto->mCryptThis(time()/35);
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($merchantID) and $dbn->isExist("select*from canteen where merchantID = '$merchantID'")){
					if(isset($name) and isset($phone) and isset($email)){
						if(is_numeric($phone)){
							if(!$dbn->isExist("select*from cashier where email = '$email' and merchantID = '$merchantID'")){
								if(!$dbn->isExist("select*from cashier where phone = '$phone' and merchantID = '$merchantID'")){
									$dbn = $dbn->connect();
									$sql = "INSERT INTO cashier (fullname, email, phone, password,  merchantID, username, addedBy, cashierID, gender, deviceID) VALUES (:fullname, :email, :phone, :password, :merchant, :username, :added, :cashier, :gender, :device)";
									$f = $dbn->prepare($sql);
									$f->bindParam(':fullname', $name);
									$f->bindParam(':email', $email);
									$f->bindParam(':phone', $phone);
									$f->bindParam(':password', $encryptedPassword);
									$f->bindParam(':merchant', $merchantID);
									$f->bindParam(':username', $email);
									$f->bindParam(':added', $username);
									$f->bindParam(':cashier', $cashID);
									$f->bindParam(':gender', $gender);
									$f->bindParam(':device', $device);
									$f->execute();
									$q2 = "update devices set merchantID = :merchant, cashierID = :cashier where deviceCode = :device";
									$f = $dbn->prepare($q2);
									$f->bindParam(':merchant', $merchantID);
									$f->bindParam(':device', $device);
									$f->bindParam(':cashier', $cashID);
									$f->execute();
									$f = new db();
									$data = $f->selectFromQuery("SELECT fullname, email, phone, merchantID, cashierID, username, deviceID FROM cashier where merchantID = '$merchantID' order by id desc");
									$data2 = $f->selectFromQuery("SELECT * FROM devices where merchantID is NULL");
									$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"cashier was successfully added", "status":"200"}, "content":{"data":'.$data.', "devices":'.$data2.'}}';
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


$app->put('/api/admin/edit/canteenCashier/{cashierID}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$name = $req->getParam('fullname');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$merchantID = $req->getParam('merchantID');
	$password = $req->getParam('password');
	$password = isset($password)? $password : $email;
	$cashierName = $req->getParam('cashierUsername');
	$cashierName = isset($cashierName)? $cashierName : $phone;
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$mcrypto = new mcrypt();
	$cashID = $req->getAttribute(cashierID);
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(true){//isset($merchantID) and $dbn->isExist("select*from canteen where merchantID = '$merchantID'")){
					if(isset($name) and isset($phone) and isset($email)){
						if(is_numeric($phone)){
							if(!$dbn->isExist("select*from cashier where email = '$email' and cashierID != '$cashID'")){
								if(!$dbn->isExist("select*from cashier where phone = '$phone' and cashierID != '$cashID'")){
									$dbn = $dbn->connect();
									$sql = "update cashier set fullname = :fullname, email = :email, phone = :phone, password = :password,  username = :username where cashierID = :cashier";
									$f = $dbn->prepare($sql);
									$f->bindParam(':fullname', $name);
									$f->bindParam(':email', $email);
									$f->bindParam(':phone', $phone);
									$f->bindParam(':password', $encryptedPassword);									
									$f->bindParam(':username', $cashierName);									
									$f->bindParam(':cashier', $cashID);
									$f->execute();
									/*$q2 = "update devices set merchantID = :merchant, cashierID = :cashier where deviceCode = :device";
									$f = $dbn->prepare($q2);
									$f->bindParam(':merchant', $merchantID);
									$f->bindParam(':device', $device);
									$f->bindParam(':cashier', $cashID);
									$f->execute();*/
									$f = new db();
									$sql = "select fullname, deviceName, deviceCode, tmp.email as email, tmp.phone as phone, tmp.merchantID as merchantID, tmp.cashierID as cashierID, tmp.name as name, tmp.totalServed as totalServed, tmp.unsettled as unsettled, username, gender from (SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, canteen.name, canteen.totalServed, canteen.unsettled, canteen.settled  username, gender FROM cashier left join canteen on cashier.merchantID = canteen.merchantID where cashier.merchantID = '".$merchantID."' order by cashier.id desc) as tmp left join devices on devices.cashierID = tmp.cashierID";
									$data = $f->selectFromQuery($sql);
									$data2 = $f->selectFromQuery("SELECT * FROM devices where merchantID is NULL");
									$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"cashier was successfully Updated", "status":"200"}, "content":{"data":'.$data.', "devices":'.$data2.'}}';
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





$app->put('/api/admin/canteenCashier/changeDevice/{to}', function(Request $req, Response $resp){
	$newDevice = $req->getAttribute('to');
	$oldDevice = $req->getParam('oldDevice');
	$cashierID = $req->getParam('cashierID');
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$merchant = $req->getParam('merchantID');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($oldDevice) and isset($newDevice) and isset($cashierID) and isset($merchant)){
					if($oldDevice != $newDevice){
						$dbn = $dbn->connect();
						$sql = "update devices set merchantID = :merchantID, cashierID = :cashier where deviceCode = :device";
						$sql2 = "update devices set merchantID = :merchantID, cashierID = :cashier where deviceCode = :device";
						$f = $dbn->prepare($sql);
						$f->bindParam(':merchantID', $merchant);
						$f->bindParam(':cashier', $cashierID);
						$f->bindParam(':device', $newDevice);
						$f->execute();
						$f = $dbn->prepare($sql2);
						$cashierID = NULL;
						$cash = NULL;
						$f->bindParam(':merchantID', $cash);
						$f->bindParam(':cashier', $cashierID);
						$f->bindParam(':device', $oldDevice);
						$f->execute();
						$db = new db();
						$sql = "select fullname, deviceName, deviceCode, tmp.email as email, tmp.phone as phone, tmp.merchantID as merchantID, tmp.cashierID as cashierID, tmp.name as name, tmp.totalServed as totalServed, tmp.unsettled as unsettled, username, gender from (SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, canteen.name, canteen.totalServed, canteen.unsettled, canteen.settled  username, gender FROM cashier left join canteen on cashier.merchantID = canteen.merchantID where cashier.merchantID = '".$merchant."' order by cashier.id desc) as tmp left join devices on devices.cashierID = tmp.cashierID";
						$data = $db->selectFromQuery($sql);
						$data2 = $db->selectFromQuery("select * from devices where merchantID is NULL and cashierID is NULL");
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
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->put('/api/admin/set/access/canteenCashier/{to}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$to = $req->getAttribute('to');
	$cashier = $req->getParam('cashierID');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(is_numeric($to) and $to == 1 or $to == 0){
					if(isset($cashier)){
						$sql = "UPDATE `cashier` set isValid = :valid where cashierID = :cashID";
						$dbn = $dbn->connect();
						$f = $dbn->prepare($sql);
						$f->bindParam(':valid', $to);
						$f->bindParam(':cashID', $cashier);
						$f->execute();
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"cashier was successfully updated", "status":"200"}, "content":{"cashierID" : "'.$cashier.'"}}';
					}else{
						$g = '{"error":{"message":"The cashierID have not been found", "status":"1"}}';
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

$app->put('/api/admin/delete/canteen/{canteenID}', function (Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$cid = $req->getAttribute("canteenID");
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				$sql = "delete from canteen where merchantID = :canteen";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindValue(":canteen", $cid);
				$f->execute();
				$sql = "delete from cashier where merchantID = :canteen";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindValue(":canteen", $cid);
				$f->execute();
				$sql = "update devices set merchantID = NULL , cashierID = NULL where merchantID = :canteen";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindValue(":canteen", $cid);
				$f->execute();
				$data = $dbn->selectFromQuery("SELECT * FROM canteen");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Canteen Purge successful", "status":"200"}, "content":{"data":'.$data.'}}';
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

$app->get('/api/admin/get/canteen/{searchParam}', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute('searchParam');
	$g = '{}';
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
						$q = "SELECT*FROM canteen order by id desc";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT*FROM canteen where name like :name or merchantID = :merchantID order by id desc";
						$f = $db->prepare($q);
						$f->execute(array(':name' => '%'.$search.'%', ':merchantID' => $search));
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
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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
			->withHeader('Access-Control-Allow-Headers', array( 'username','X-Requested-With', 'Origin', 'Content-Type', 'Authorization', 'X-Auth-Token'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->get('/api/admin/get/canteenCashier/{merchantID}', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute('merchantID');
    if(array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					if($search == "-"){
						$q = "select fullname, deviceName, deviceCode, tmp.email as email, tmp.phone as phone, tmp.merchantID as merchantID, tmp.cashierID as cashierID, tmp.name as name, tmp.totalServed as totalServed, tmp.unsettled as unsettled, username, gender from (SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, canteen.name, canteen.totalServed, canteen.unsettled, canteen.settled  username, gender FROM cashier left join canteen on cashier.merchantID = canteen.merchantID order by cashier.id desc) as tmp left join devices on devices.cashierID = tmp.cashierID";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						//$q = "SELECT fullname, email, phone, merchantID, cashierID, username, gender FROM cashier where merchantID = :name order by id desc";
						$q = "select fullname, deviceName, deviceCode, tmp.email as email, tmp.phone as phone, tmp.merchantID as merchantID, tmp.cashierID as cashierID, tmp.name as name, tmp.totalServed as totalServed, tmp.unsettled as unsettled, username, gender from (SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, canteen.name, canteen.totalServed, canteen.unsettled, canteen.settled,  cashier.username as username, gender FROM cashier left join canteen on cashier.merchantID = canteen.merchantID where cashier.merchantID = :name order by cashier.id desc) as tmp left join devices on devices.cashierID = tmp.cashierID";
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
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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
$app->post('/api/admin/delete/canteenCashier/{cashierID}', function (Request $req, Response $resp){
	$cashierID = $req->getAttribute('cashierID');
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	if(isset($username) and isset($pkey)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				$sql = "delete from cashier where cashierID = :cashier";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindParam(':cashier', $cashierID);
				$f->execute();
				$sql = "update devices set cashierID = NULL , merchantID = NULL where cashierID = :cashier";
				$f = $dbn->connect();
				$f = $f->prepare($sql);
				$f->bindParam(':cashier', $cashierID);
				$f->execute();
				$data = $dbn->selectFromQuery("SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashierID, canteen.name, canteen.totalServed, canteen.unsettled, canteen.settled  username, gender FROM cashier left join canteen on cashier.merchantID = canteen.merchantID order by cashier.id desc");
				$data2 = $f->selectFromQuery("SELECT * FROM devices where merchantID is NULL");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"CanteenCashier Purge successful", "status":"200"}, "content":{"data":'.$data.', "devices":'.$data2.'}}';
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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
$app->post('/api/admin/create/staff', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$data = $req->getParam('staffs');
	$data = isset($data)? urldecode($data) : $data;
	$data = is_array($data)? json_encode($data) : $data;
	//$data = json_encode($req->getParam('staffs'));
	//die(urldecode($data));
	$dataz = '[]';
	$dat = '[]';
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if($dbn->isJson($data)){
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[]);
					$staffDetails = json_decode($data);
					$datum = $staffDetails->data;
					$count = 0;
					$staffCount = count($datum);
					$date = time();
					while($count < $staffCount){
						$name = $datum[$count]->name;
						$staffID = $datum[$count]->staffID;
						$cardSerial = $datum[$count]->cardSerial;
						$staffType = $datum[$count]->staffType;
						$location = $datum[$count]->location;
						$designation = $datum[$count]->designation;
						$department = $datum[$count]->department;
						$status = 1;
						if(isset($name) and isset($staffID) and isset($cardSerial) and isset($staffType) and isset($location)){
							if(true){
								if(true){
									if(true){
										try{
											$q = "insert into staffs (fullname, staffID, cardSerial, staffType, location, status, department, designation) values (:name, :staffID, :cardSerial, :staffType, :location, :status, :department, :designation)";
											$dbn = new db();
											$dbn = $dbn->connect();
											$f = $dbn->prepare($q);
											$f->bindParam(':name', $name);
											$f->bindParam(':staffID', $staffID);
											$f->bindParam(':cardSerial', $cardSerial);
											$f->bindParam(':staffType', $staffType);
											$f->bindParam(':location', $location);
											$f->bindParam(':status', $status);
											$f->bindParam(':department', $department);
											$f->bindParam(':designation', $designation);
											$f->execute();
											$q = "update cards set assigned = 1, valid = 1 where cardSerial = :card";
											$f = $dbn->prepare($q);
											$f->bindParam(':card', $cardSerial);
											$f->execute();
											$f = new db();
											array_push($statuses["successful"], $datum[$count]->staffID);
										}catch(PDOException $ex){
											array_push($statuses["duplicate"], $datum[$count]->staffID);
										}
										$f = new db();
										$dat = $f->selectFromQuery("SELECT * FROM staffs");
										$dataz = $f->selectFromQuery("SELECT * FROM cards where assigned is NULL or assigned = 0");
									}else{
										$g = '{"error":{"messag
											e":"Discount, goldClubDiscount, othersDiscount must be numeric", "status":"1"}}';
									}
								}else{
									$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
								}
							}else{
								$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
							}
						}else{
							array_push($statuses["failed"], $datum[$count]->staffID);
						}
						$count++;
					}
					$statuses = json_encode($statuses);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"The New Staff was created","code":"200"}, "content":{"data":'.$statuses.', "cards":'.$dataz.', "staffs":'.$dat.'}}';
				}else{
					$g = '{"error":{"message":"The staffs is not a valid JSON. Array of staffs is Expected", "status":"1"}}';
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
$app->put('/api/admin/delete/staff/{staffID}', function (Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$cardSerial = $req->getParam('cardSerial');
	$staffID = $req->getAttribute('staffID');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				$sql = "delete from staffs where staffID = :staffID";
				$dbn = $dbn->connect();
				$f = $dbn->prepare($sql);
				$f->bindParam(':staffID', $staffID);
				$f->execute();
				$sql = "update cards set assigned = 0, valid = 0 where cardSerial = :card";
				$f = $dbn->prepare($sql);
				$f->bindParam(':card', $cardSerial);
				$f->execute();
				$dbn = new db();
				$data = $dbn->selectFromQuery("SELECT fullname, staffID, cardSerial, status, staffType as stafft, location, department, designation, userTypes.userType as staffType FROM staffs left join userTypes on staffs.staffType = userTypes.id order by staffs.id desc");
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Action on Staff successful", "status":"200"}, "content":{"data":'.$data.'}}';
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
$app->put('/api/admin/update/staff', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$staffID = $req->getParam('staffID');
	$cardSerial = $req->getParam('cardSerial');
	$newCardSerial = $req->getParam('newCardSerial');
	$newCardSerial = isset($newCardSerial)? $newCardSerial : $cardSerial;
	$staffType = $req->getParam('staffType');
	$location = $req->getParam('location');
	$status = 1;//$req->getParam('status');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($staffID) and isset($cardSerial) and isset($staffType) and isset($location)){
					if(true){
						if(true){
							if(true){
								$q = "update staffs set fullname = :name, cardSerial = :cardSerial, staffType = :staffType, location = :location, status = :status where staffID = :staffID";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':staffID', $staffID);
								$f->bindParam(':cardSerial', $newCardSerial);
								$f->bindParam(':staffType', $staffType);
								$f->bindParam(':location', $location);
								$f->bindParam(':status', $status);
								$f->execute();
								$q = "update cards set assigned = 1, valid = 1 where cardSerial = :card";
								$f = $dbn->prepare($q);
								$f->bindParam(':card', $cardSerial);
								$f->execute();
								if(isset($newCardSerial) and $newCardSerial != $cardSerial){
									$sq1 = "update cards set assigned = 0, valid = 0 where cardSerial = :card";
									$sq2 = "update cards set assigned = 1 where cardSerial = :card";
									$f = $dbn->prepare($sq1);
									$f->bindParam(':card', $cardSerial);
									$f->execute();
									$f = $dbn->prepare($sq2);
									$f->bindParam(':card', $newCardSerial);
									$f->execute();
								}
								$f = new db();
								$data = $f->selectFromQuery("SELECT fullname, staffID, cardSerial, status, staffType as stafft, location, department, designation, userTypes.userType as staffType FROM staffs left join userTypes on staffs.staffType = userTypes.id order by staffs.id desc");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Staff update successful", "status":"200"}, "content":{"data":'.$data.'}}';
							}else{
								$g = '{"error":{"message":"Discount, goldClubDiscount, othersDiscount must be numeric", "status":"1"}}';
							}
						}else{
							$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
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


$app->post('/api/admin/create/guest', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$host = $req->getParam('host');
	$cardSerial = $req->getParam('cardSerial');
	$guestType = $req->getParam('guestType');
	$transdate = time();
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($cardSerial) and $cardSerial > 0 and isset($guestType) and $guestType > 0){
					if(true){
						if(true){
							if(true){
								$q = "insert into guests (guestName, host, cardSerial, generatedBy, transdate, userType) values (:name, :host, :cardSerial, :generatedBy, :transDate, :usertype)";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':host', $host);
								$f->bindParam(':cardSerial', $cardSerial);
								$f->bindParam(':generatedBy', $username);
								$f->bindParam(':transDate', $transdate);
								$f->bindParam(':usertype', $guestType);
								$f->execute();
								$q = "update cards set assigned = 1, valid = 1 where cardSerial = :card";
								$f = $dbn->prepare($q);
								$f->bindParam(':card', $cardSerial);
								$f->execute();
								$f = new db();
								$data = $f->selectFromQuery("SELECT * FROM guests");
								$data2 = $f->selectFromQuery("SELECT * FROM cards where assigned = 0");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"guest creation successful", "status":"200"}, "content":{"data":'.$data.', "cards":'.$data2.'}}';
							}else{
								$g = '{"error":{"message":"Discount, goldClubDiscount, othersDiscount must be numeric", "status":"1"}}';
							}
						}else{
							$g = '{"error":{"message":"Account Number is invalid", "status":"1"}}';
						}
					}else{
						$g = '{"error":{"message":"Phone Number is invalid", "status":"1"}}';
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

$app->put('/api/admin/unlink/guest', function (Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$guestID = $req->getParam('guestID');
	$cardSerial = $req->getParam('cardSerial');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($guestID) and isset($cardSerial)){
					$s1 = "update guests set valid = 0 where id = :id and cardSerial = :cardSerial";
					$s2 = "update cards set assigned = 0 where cardSerial = :cardSerial";
					$db = $dbn->connect();
					$f = $db->prepare($s1);
					$f->bindParam(":id",$guestID);
					$f->bindParam(":cardSerial",$cardSerial);
					$f->execute();
					$f = $db->prepare($s1);
					$f->bindParam(":cardSerial",$cardSerial);
					$f->execute();
					$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"guest umlink successful", "status":"200"}, "content":{"data":""}}';
				}else{
					$g = '{"error":{"message":"All the required parameters are not found", "status":"1"}}';
				}
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

$app->get('/api/admin/get/departments', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute('searchParam');
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
		$username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				$q = "select distinct department from staffs";
				$db = $dbn->connect();
				$f = $db->prepare($q);
				$f->execute();
				$row = $f->fetchAll();
				if($row){
					$data = json_encode($row, true);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$data = "[]";
				}
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Admin Credentials are required", "status":"1"}}';
	}
	return $resp->withStatus(200)
	->withHeader('Content-Type', 'application/json')
	->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization', 'someValue'))
	->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	->write($g);
});
$app->get('/api/admin/get/guest/{searchParam}', function(Request $req, Response $resp){
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
						$q = "SELECT cards.staffI as staffID, tmp.id as id, guestName, host, generatedBy, canteenName, canteenID, cashier, amount, syncDate, cardBalance, tmp.cardSerial as cardSerial, canteenSettled, canteenUnsettled, operator, guestBillSettled FROM (SELECT guests.id as id, guestName, host, generatedBy, canteen.name as canteenName, guests.canteenID as canteenID, cashier, amount, syncDate, cardBalance, cardSerial, canteen.settled as canteenSettled, canteen.unsettled as canteenUnsettled, canteen.operator as operator,  guests.settled as guestBillSettled FROM guests left join canteen on canteen.merchantID = guests.canteenID ) as tmp left join cards on cards.cardSerial = tmp.cardSerial ";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT guests.id as transactionID, guestName, host, generatedBy, canteen.name as canteenName, guests.canteenID as canteenID, cashier, amount, syncDate, cardBalance, cardSerial, canteen.settled as canteenSettled, canteen.unsettled as canteenUnsettled, canteen.operator,  guests.settled as guestBillSettled FROM guests left join canteen on canteen.merchantID = guests.canteenID where guestName like :name or host = :merchantID order by id desc";
						$f = $db->prepare($q);
						$f->execute(array(':name' => '%'.$search.'%', ':merchantID' => $search));
					}
					if(array_key_exists('HTTP_SOMEVALUE', $headers)){
						$q = "select * from guests where valid = 1";
						$f = $db->prepare($q);
						$f->execute();
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
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization', 'someValue'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});


$app->get('/api/admin/get/staff/{searchParam}', function (Request $req, Response $resp){
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
						$q = "SELECT fullname, staffID, cardSerial, status, staffType as stafft, location, department, designation, userTypes.userType as staffType FROM staffs left join userTypes on staffs.staffType = userTypes.id order by staffs.id desc";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT fullname, staffID, cardSerial, status, staffType as stafft, location, department, designation, userTypes.userType as staffType FROM staffs left join userTypes on staffs.staffType = userTypes.id where fullname like :name or staffID = :merchantID order by staffs.id desc";
						$f = $db->prepare($q);
						$f->execute(array(':name' => '%'.$search.'%', ':merchantID' => $search));
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
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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
						$sql = "select tmp_t.cardSerial as cardSerial, tmp_t.assigned as assigned, tmp_t.valid as valid, dateCreated, dateSynced staffID, fullname, staffType, location, guestName from (select cards.cardSerial, assigned, valid, dateCreated, dateSynced, staffID, fullname, staffType, location from cards left join staffs on staffs.cardSerial = cards.cardSerial) as tmp_t left join guests on tmp_t.cardSerial = guests.cardSerial";
						$f = new db();
						$data = $f->selectFromQuery($sql);
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Staff was successfully updated", "status":"200"}, "content":{"cardSerial" : "'.$cardSerial.'", "cards":'.$data.'}}';
					}else{
						$g = '{"error":{"message":"The staffID have not been found", "status":"1"}}';
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


$app->get('/api/admin/get/profile', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = "f";
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey' and (canteenID is NULL or canteenID = '')";
		try{
			$dbn = new db();
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
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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

$app->get('/api/admin/get/transactionsSummary/{user}/{canteenID}/{from}/{to}/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute("canteenID");
	$from = intval($req->getAttribute("from"));
	$to = ($req->getAttribute("to") > time())? strtotime('today midnight') : $req->getAttribute("to") + 86399;
	$userType = $req->getAttribute("user");
	$type = $req->getAttribute("type");
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search) and isset($to) and isset($from)){					
					$binds = [];
					$binds[':from'] = $from; $binds['to'] = $to; $binds[':merchant'] = $search;
					$st = "select*from transactions where (transDate >= :from and transDate <= :to) and canteenID = :merchant and settled = 0";
					$jigga = new db();
					$jigga = $jigga->connect();
					$fi = $jigga->prepare($st);
					$fi->execute($binds);
					$row = $fi->fetchAll();
					$tam = 0;
					if($row){
						$data = json_encode($row, true);
						$data = json_decode($data,true);
					}else{
						$data = array();
					}
					$st = "select*from guests where (transDate >= :from and transDate <= :to) and canteenID = :merchant and settled = 0";
					$fi = $jigga->prepare($st);
					$fi->execute($binds);
					$row = $fi->fetchAll();
					$tamo = 0;
					if($row){
						$dataa = json_encode($row, true);
						$dataa = json_decode($dataa,true);
					}else{
						$dataa = [];
					}
					$newList = array();
					$to = ($to < 1504224000)? 1504224000 : $to;
					while($from <= ($to + 86399)){
						$aUser = array();
						$aUser['transDate'] = $from;
						$thisFound = false;
						$grandTotal = 0;
						for($i = 0; $i < count($data); $i++){
							$ut = $data[$i]['userType'];
							$date = $data[$i]['transDate'];
							if($date >= $from and $date <= ($from + 86399)){
								$thisFound = true;
								if(array_key_exists($ut, $aUser)){
									$aUser[$ut] = $aUser[$ut] + 1;
									$aUser[$ut.' total'] += $data[$i]['amount'];
								}else{
									$aUser[$ut] = 1;
									$aUser[$ut.' total'] = $data[$i]['amount'];
								}
								$grandTotal += $data[$i]['amount'];
							}else{

							}
						}
						for($i = 0; $i < count($dataa); $i++){
							$ut = 'Guests';
							$date = $dataa[$i]['transDate'];
							if($date >= $from and $date <= ($from + 86399)){
								$thisFound = true;
								if(array_key_exists($ut, $aUser)){
									$aUser[$ut] = $aUser[$ut] + 1;
									$aUser[$ut.' total'] += $dataa[$i]['amount'];
								}else{
									$aUser[$ut] = 1;
									$aUser[$ut.' total'] = $dataa[$i]['amount'];
								}
								$grandTotal += $dataa[$i]['amount'];
							}else{

							}
						}
						if($thisFound){ $aUser['GrandTotal'] = $grandTotal; array_push($newList, $aUser);}
						$from += 86400;
					}
					$allUser = json_encode($newList);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$allUser.'}}';
				}else{
					$g = '{"error":{"message":"Search is required", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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

$app->post('/api/admin/purge/system', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$magic = $req->getParam('magic');
	if(isset($username) and isset($pkey) and isset($magic)){
		$sql = "select*from admin where username = '$username' and publicKey = '$pkey' and canteenID is NULL";
		if($magic == "AdministratoR"){
			try{
				$dbn = new db();
				if($dbn->isExist($sql)){
					if(true){
						if(true){
							$sql = "DELETE FROM staffs";
							$sql2 = "DELETE FROM guests";
							$sql3 = "UPDATE cards set valid = 1, assigned = 0";
							$sql4 = "DELETE FROM transactions";
							$sql5 = "UPDATE CANTEEN SET settled = 0, unsettled = 0, totalServed = 0";
							$dbn = $dbn->connect();
							$f = $dbn->prepare($sql);
							$f->execute();
							$f = $dbn->prepare($sql2);
							$f->execute();
							$f = $dbn->prepare($sql3);
							$f->execute();
							$f = $dbn->prepare($sql4);
							$f->execute();
							$f = $dbn->prepare($sql5);
							$f->execute();
							$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"The Purge was succesful", "status":"200"}, "content":{}}';
						}else{
							$g = '{"error":{"message":"The staffID have not been found", "status":"1"}}';
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
			$g = '{"error":{"message":"Validation Errors. Admin profile not found using '.$magic.'", "status":"1"}}';
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

$app->get('/api/admin/get/transactions/{user}/{canteenID}/{from}/{to}/{type}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute("canteenID");
	$from = $req->getAttribute("from");
	$to = ($req->getAttribute("to") > time())? time() + 86399 : $req->getAttribute("to") + 86399;
	$userType = $req->getAttribute("user");
	$type = $req->getAttribute("type");
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search) and isset($to) and isset($from)){
					$db = $dbn->connect();
					$binds = [];
					if($search == "-") { $ssq = "";} else { $ssq = "where merchantID = :merchant";}
					if($ssq == "") $ssq = "where transDate >= :from and transDate <= :to"; else $ssq = $ssq." and (transDate >= :from and transDate <= :to )";
					if($userType == "guest") $patch = "guests."; else $patch = "transactions.";
					if($type == "unsettled") $ssq = $ssq." and (".$patch."settled = 0 )"; elseif($type == "settled") $ssq = $ssq." and (".$patch."settled = 1)"; else $ssq = $ssq." and (".$patch."settled = 0 or ".$patch."settled = 1)";
					$q = "SELECT transactionID, department, designation, staffID, staffName, staffType, staffLocation, canteenName, canteenID, cashier, amount, syncDate, cardBalance, cardSerial, tmp_tab2.settled as isSettled, userTypes.userType as usertype, canteenOperator, canteenSettled from (SELECT tmp_tab.transactionID as transactionID, staffs.staffID as staffID, staffs.department as department, staffs.designation as designation, staffs.fullname as staffName, staffs.staffType as staffType, staffs.location as staffLocation, tmp_tab.canteenName as canteenName, canteenID, tmp_tab.cashier as cashier, tmp_tab.amount as amount, tmp_tab.syncDate as syncDate, tmp_tab.cardBalance as cardBalance, tmp_tab.cardSerial as cardSerial, canteenOperator, canteenSettled, tmp_tab.settled from (SELECT transactions.id as transactionID, canteen.name as canteenName, transactions.canteenID as canteenID, cashier, amount, syncDate, cardBalance, cardSerial,  transactions.settled, canteen.operator as canteenOperator, canteen.settled as canteenSettled FROM transactions left join canteen on canteen.merchantID = transactions.canteenID ".$ssq." ) as tmp_tab left join staffs on staffs.cardSerial = tmp_tab.cardSerial) as tmp_tab2 left join userTypes on userTypes.id = tmp_tab2.staffType";
					if($userType == "guest") $q = "SELECT guests.id as transactionID, guestName, host, generatedBy, canteen.name as canteenName, guests.canteenID as canteenID, cashier, amount, syncDate, cardBalance, cardSerial, canteen.settled as canteenSettled, canteen.unsettled as canteenUnsettled, canteen.operator as canteenOperator,  guests.settled as guestBillSettled FROM guests left join canteen on canteen.merchantID = guests.canteenID ".$ssq."";
					$f = $db->prepare($q);
					//$binds = array(':from' => $from, ':to'=> $to);
					$binds[':from'] = $from; $binds[':to'] = $to;
					if($search != "-"){ $binds[':merchant'] = $search; }
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
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
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
$app->put('/api/admin/settle/payment/{type}', function (Request $req, Response $resp){
	$type = $req->getAttribute('type');
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$transID = $req->getParam('transID');
	if(isset($type) and isset($username) and isset($publicKey) and isset($transID)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				$transID = explode(",", $transID);
				$counter = 0;
				$db = $dbn->connect();
				if($type == "staff" or $type == "1"){
					while ($counter < count($transID)){
						$tr = trim($transID[$counter]);
						$r = "update transactions set settled = 1 where id = :id and settled = 0";
						$f = $db->prepare($r);
						$f->bindValue(":id", $tr);
						$f->execute();
						if($f->rowCount()){
							$a = "select amount, canteenID from transactions where id = :id";
							$f = $db->prepare($a);
							$f->execute(array(':id' => $tr));
							$row = $f->fetchAll();
							if($row){
								$data = json_encode($row, true);
								$data = json_decode($data,true);
								$q = "update canteen set unsettled = unsettled - :amount, settled = settled + :amount where merchantID = :mID";
								$f = $db->prepare($q);
								$f->bindValue(":amount", $data[0]['amount']);
								$f->bindValue(":mID", $data[0]['canteenID']);
								$f->execute();
							}
								
						}
						$counter++;
					}
					$q = "SELECT tmp_tab.transactionID as transactionID, tmp_tab.canteenName as canteenName, canteenID, tmp_tab.cashier as cashier, tmp_tab.amount as amount, tmp_tab.syncDate as syncDate, tmp_tab.cardBalance as cardBalance, tmp_tab.cardSerial as cardSerial, settled from (SELECT transactions.id as transactionID, canteen.name as canteenName, transactions.canteenID as canteenID, cashier, amount, syncDate, cardBalance, cardSerial,  transactions.settled FROM transactions left join canteen on canteen.merchantID = transactions.canteenID ) as tmp_tab left join staffs on staffs.cardSerial = tmp_tab.cardSerial";
				}else{
					while ($counter < count($transID)){
						$tr = trim($transID[$counter]);
						$r = "update guests set settled = 1 where id = :id and settled = 0";
						$f = $db->prepare($r);
						$f->bindValue(":id", $tr);
						$f->execute();
						if($f->rowCount()){
							$a = "select amount, canteenID from guests where id = :id";
							$f = $db->prepare($a);
							$f->execute(array(':id' => $tr));
							$row = $f->fetchAll();
							if($row){
								$data = json_encode($row, true);
								$data = json_decode($data,true);
								$q = "update canteen set unsettled = unsettled - :amount, settled = settled + :amount where merchantID = :mID";
								$f = $db->prepare($q);
								$f->bindValue(":amount", $data[0]['amount']);
								$f->bindValue(":mID", $data[0]['canteenID']);
								$f->execute();
							}							
						}
						$counter++;
					}
					$q = "SELECT guests.id as transactionID, guestName, host, generatedBy, canteen.name as canteenName, guests.canteenID as canteenID, cashier, amount, syncDate, cardBalance, cardSerial,  canteen.settled FROM guests left join canteen on canteen.merchantID = guests.canteenID";
				}
				$data = $dbn->selectFromQuery($q);
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Bills has been settled", "status":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
			}
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Priori not found. Admin Credentials are required... '.$username.'", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/canteenCashier/getCards', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	if(isset($username) and isset($publicKey)){
		$q = "SELECT*FROM cashier WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			$dbn->reverseCards();
			if($dbn->isExist($q)){
				$data2 = $dbn->selectFromQuery("select cardSerial from cards where valid = 0");
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"cards":'.$data2.'}}';
			}else{
				$g = '{"error":{"message":"Supplied Credentials are invalid", "status":"1"}}';
			}
		}catch(PDOException $e){
			$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
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
$app->post('/api/canteenCashier/login', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	if(isset($username) and isset ($password)){
		$sql = "SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, cashier.username, canteen.name from cashier left join canteen on canteen.merchantID = cashier.merchantID where cashier.username = :username and cashier.password = :pass";
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
				$sql2 = "update cashier set publicKey = :fkey, lastSeen = :last where username = :user and password = :pass";
				$dbn = new db();
				$db = $dbn->connect();
				$f = $db->prepare($sql2);
				$f->bindParam(':fkey', $fkey);
				$f->bindParam(':last', $isdate);
				$f->bindParam(':user', $username);
				$f->bindParam(':pass', $password);
				$f->execute();
				$db = new db();
				$sql = "SELECT fullname, cashier.merchantID, cashier.cashierID, cashier.username, cashier.publicKey from cashier left join canteen on canteen.merchantID = cashier.merchantID where cashier.username = '$username' and cashier.password = '$password'";
				$data = $db->selectFromQuery($sql);
				$data = substr($data,1,strlen($data)-2);
				//$data2 = $db->selectFromQuery("selectfrom cards where valid = 1");
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
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


$app->post('/api/alter/password/canteenCashier', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('oldPassword');
	$password2 = $req->getParam('newPassword');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	$string = $key.'34iIlm'.$password2.'io9m-';
	$password2 = hash('sha256', $string);
	if(isset($username) and isset ($password)){
		$sql = "SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, cashier.username, canteen.name from cashier left join canteen on canteen.merchantID = cashier.merchantID where cashier.username = :username and cashier.password = :pass";
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
				$sql2 = "update cashier set password = :password2, publicKey = :fkey, lastSeen = :last where username = :user and password = :pass";
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
					$valid = 0;
					while($count < $cardCount){
						if(isset($datum[$count]->cardSerial) and isset($datum[$count]->issuerID) and isset($datum[$count]->dateCreated)){
							$q = "INSERT INTO cards (cardSerial, issuerID, dateCreated, dateSynced, staffI, valid) SELECT * FROM (SELECT :cardSerial, :issuerID, :dateCreated, :dateSynced, :staffI, :valid) AS tmp WHERE NOT EXISTS (SELECT cardSerial FROM cards WHERE cardSerial = :cardSerial or staffI = :staffI) LIMIT 1";
							$db = $dbn->connect();
							$f = $db->prepare($q);
							$f->bindValue(":cardSerial", $datum[$count]->cardSerial);
							$f->bindValue(":issuerID", $datum[$count]->issuerID);
							$f->bindValue(":dateCreated", $datum[$count]->dateCreated);
							$f->bindValue(":staffI", $datum[$count]->staffID);
							$f->bindValue(":dateSynced", $date);
							$f->bindValue(":valid", $valid);
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
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"Sync Sync","code":"200"}, "content":{"data":'.$statuses.', "cards":'.$data.'}}';
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

$app->post('/api/admin/create/devices', function(Request $req, Response $resp){
	$deviceCode = $req->getParam("deviceCode");
	$deviceName = $req->getParam("deviceName");
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	if(isset($username) and isset($publicKey) and isset($deviceCode) and isset($deviceName)){
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
					if($type == "unAssigned") $sQ = " where devices.merchantID is NULL";
					if($type == "assigned") $sQ = " where devices.merchantID not NULL";
				}
				$sql = "SELECT tmp.deviceCode as deviceCode, cashier.fullname as fullname, tmp.deviceName as deviceName, tmp.cashierID as cashierID, tmp.name as name, tmp.totalServed as totalServed, tmp.unsettled as unsettled, tmp.operator as operator from (select devices.deviceCode, devices.deviceName, devices.cashierID, canteen.name, canteen.totalServed, canteen.unsettled, canteen.operator from devices left join canteen on devices.merchantID = canteen.merchantID ".$sQ.") as tmp left join cashier on cashier.cashierID = tmp.cashierID";
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
			$dbn->reverseCards();
			if($dbn->isExist($q)){
				$sQ = "";
				$db = $dbn->connect();
				if(isset($type)){
					if($type == "-") $sQ = "";
					elseif($type == "unAssigned") $sQ = " where cards.assigned = 0";
					elseif($type == "assigned") $sQ = " where cards.assigned = 1";
					else $sQ = " where cards.staffI = '$type'";
				}
				if(isset($cardSerial)){
					$clause = (strlen($sQ) > 1)? " and " : " where ";
					$sQ = $sQ.$clause." cards.cardSerial = '".$cardSerial."'";}
				$sql = "select tmp_t.cardSerial as cardSerial, tmp_t.assigned as assigned, tmp_t.valid as valid, tmp_t.staffI as staffI, dateCreated, dateSynced, staffID, fullname, staffType, location, guestName from (select cards.staffI as staffI, cards.cardSerial, assigned, valid, dateCreated, dateSynced, staffID, fullname, staffType, location from cards left join staffs on staffs.cardSerial = cards.cardSerial ".$sQ.") as tmp_t left join guests on tmp_t.cardSerial = guests.cardSerial";
				//$sql = "select cards.cardSerial, assigned, valid, dateCreated, dateSynced staffID, fullname, staffType, location from cards left join staffs on staffs.cardSerial = cards.cardSerial".$sQ;
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
$app->post('/api/admin/add/userType', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$userType = $req->getParam('usertype');
	$mealLim = $req->getParam('mealLim');
	if(isset($username) and isset($publicKey)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		$dbn = new db();
		if($dbn->isExist($q)){
			if(isset($userType) and isset($mealLim) and is_numeric($mealLim)){
				try{
					$q = "INSERT INTO userTypes (userType, mealLim) SELECT * FROM (SELECT :usertype, :mealLim) AS tmp WHERE NOT EXISTS (SELECT userType FROM userTypes WHERE userType = :usertype) LIMIT 1";
					$db = $dbn->connect();
					$f = $db->prepare($q);
					$f->bindParam(':usertype', $userType);
					$f->bindParam(':mealLim', $mealLim);
					$f->execute();
					$data = $dbn->selectFromQuery("select*from userTypes");
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"New addition has been Succesfull","code":"200"}, "content":{"data":'.$data.'}}';
				}catch(PDOException $ex){
					$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"All parameters are required. Meal Limit amount must be a number", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
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
$app->put('/api/admin/edit/userType/{id}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$userType = $req->getParam('usertype');
	$mealLim = $req->getParam('mealLim');
	$id = $req->getAttribute('id');
	if(isset($username) and isset($publicKey)){
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		$dbn = new db();
		if($dbn->isExist($q)){
			if(isset($userType) and isset($mealLim) and is_numeric($mealLim)){
				$q = "update userTypes set userType = :usertype, mealLim = :mealLim WHERE id = :id";
				$db = $dbn->connect();
				$f = $db->prepare($q);
				$f->bindParam(':usertype', $userType);
				$f->bindParam(':mealLim', $mealLim);
				$f->bindParam(':id', $id);
				$f->execute();
				$data = $dbn->selectFromQuery("select*from userTypes");
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"New Update has been Succesfull","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"All parameters are required. Meal Limit amount must be a number", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
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
$app->get('/api/admin/get/userType/{id}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$type = $req->getAttribute("type");
	$id = $req->getAttribute('id');
	if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)){
		$publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		$dbn = new db();
		if($dbn->isExist($q)){
			if(true){
				if($id == "-") $ender = ""; else $ender = " WHERE id = '$id'";
				$data = $dbn->selectFromQuery("select*from userTypes".$ender);
				$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"All parameters are required. Meal Limit amount must be a number", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
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
$app->put('/api/admin/delete/userType/{id}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$publicKey = $req->getParam('publicKey');
	$id = $req->getAttribute('id');
	try{
		if(isset($username) and isset($publicKey)){
			$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($id)){
					$q = "delete from userTypes WHERE id = :id";
					$db = $dbn->connect();
					$f = $db->prepare($q);
					$f->bindParam(':id', $id);
					$f->execute();
					$dbn = new db();
					$data = $dbn->selectFromQuery("select*from userTypes");
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"New addition has been Succesfull","code":"200"}, "content":{"data":'.$data.'}}';
				}else{
					$g = '{"error":{"message":"All parameters are required. Meal Limit amount must be a number", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Username and key does not match any User", "status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Priori not found. Credentials are required", "status":"1"}}';
		}
	}catch(PDOException $ex){
		$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/cashier/sync/transactions', function(Request $req, Response $resp){
	$cashier = $req->getParam('cashierUsername');
	$cashierID = $req->getParam('cashierID');
	$merchant = $req->getParam('canteenID');
	$transactions = $req->getParam('transactions');
	$date = time();
	if(isset($cashier) and isset($cashierID) and isset($merchant)){
		$sql = "SELECT*FROM cashier where username = '$cashier' and cashierID = '$cashierID' and merchantID = '$merchant'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				$transactions = urldecode($transactions);
				if($dbn->isJson($transactions)){
					$imDB = new db();
					$imDB = $imDB->connect();
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[], "Internal-Failure"=>[]);
					$transactions = json_decode($transactions);
					$transCount = count($transactions->data);
					$datum = $transactions->data;
					$batchID = time()."/".substr($cashier, 0,3)."/".substr($merchant,0,3)."/".rand(10000,100000000);
					$f = 0;
					while($f < $transCount){
						if(isset($datum[$f]->cardSerial) and strlen($datum[$f]->cardSerial) > 0 and isset($datum[$f]->transDate) and strlen($datum[$f]->transDate) > 0 and isset($datum[$f]->transID) and strlen($datum[$f]->transID) > 0){
							$sql2 = "select userTypes.userType as userType, staffType, userTypes.mealLim as mealLim from staffs left join userTypes on userTypes.id = staffs.staffType where staffs.cardSerial = '".$datum[$f]->cardSerial."'";
							$sql3 = "select guests.userType, guests.host, guestName, userTypes.mealLim as mealLim from guests left join userTypes on  userTypes.id = guests.userType where cardSerial = '".$datum[$f]->cardSerial."' and valid = 1";
							if($dbn->isExist($sql2, false)){								
								//die('mill');
								$dbi = $dbn->connect();
								$fi = $dbi->prepare($sql2);
								$fi->execute();
								$row = $fi->fetchAll();
								if($row){
									$data = json_encode($row, true);
									$data = json_decode($data,true);
									$aMount = $data[0]['mealLim'];
									$usert = $data[0]['userType'];
								}
								$sql = "SELECT * FROM transactions where transID = '".$datum[$f]->transID."' and cardSerial = '".$datum[$f]->cardSerial."' and canteenID = '".$merchant."'";
								if(!$dbn->isExist($sql)){
									$q = "insert into transactions (canteenID, cashier, amount, syncDate, cardSerial, transDate, transID, userType) values (:merchant, :cashier, :amount, :syncDate, :cardSerial, :transDate, :transID, :ut)";
									$fa = $imDB->prepare($q);
									$fa->bindValue(':merchant', $merchant);
									$fa->bindValue(':cashier', $cashier);
									$fa->bindValue(':amount', $aMount);
									$fa->bindValue(':syncDate', $date);
									$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
									$fa->bindValue(':transDate', round(floatval($datum[$f]->transDate)/1000));
									$fa->bindValue(':transID', $datum[$f]->transID);
									$fa->bindValue(':ut', $usert);
									$fa->execute();
									$qa = "update canteen set unsettled = unsettled + :amount, totalServed = totalServed + 1 where merchantID = :merchant";
									$fa = $imDB->prepare($qa);
									$fa->bindValue(':merchant', $merchant);
									$fa->bindValue(':amount', $aMount);
									array_push($statuses["successful"], $datum[$f]->transID);
									$fa->execute();
								}else{
									array_push($statuses["duplicate"], $datum[$f]->transID);
								}
								$dbn->proccessLimit($datum[$f]->cardSerial);
							}elseif($dbn->isExist($sql3)){
								$sql = "SELECT * FROM guests where transID = '".$datum[$f]->transID."' and cardSerial = '".$datum[$f]->cardSerial."' and canteenID = '".$merchant."' and settled = 0";
								if(!$dbn->isExist($sql)){
									$dbi = $dbn->connect();
									$fi = $dbi->prepare($sql3);
									$fi->execute();
									$row = $fi->fetchAll();
									if($row){
										$data = json_encode($row, true);
										$data = json_decode($data,true);
										$aMount = $data[0]['mealLim'];
									}else{
										$aMount = 0;
									}
									$q = "update guests set canteenID = :merchant, cashier = :cashier, valid = 0, amount = :amount, syncDate = :syncDate, transDate = :transDate, transID = :transID where cardSerial = :cardSerial";
									$fa = $imDB->prepare($q);
									$fa->bindValue(':merchant', $merchant);
									$fa->bindValue(':cashier', $cashier);
									$fa->bindValue(':amount', $aMount);
									$fa->bindValue(':syncDate', $date);
									$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
									$fa->bindValue(':transDate', $datum[$f]->transDate);
									$fa->bindValue(':transID', $datum[$f]->transID);
									$fa->execute();
									$q = "update cards set assigned = 0, valid = 0 where cardSerial = :cardSerial";
									$fa = $imDB->prepare($q);
									$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
									$fa->execute();
									$qa = "update canteen set unsettled = unsettled + :amount, totalServed = totalServed + 1 where merchantID = :merchant";
									$fa = $imDB->prepare($qa);
									$fa->bindValue(':merchant', $merchant);
									$fa->bindValue(':amount', $aMount);
									array_push($statuses["successful"], $datum[$f]->transID);
								}else{
									array_push($statuses["duplicate"], $datum[$f]->transID);
								}
							}else{
								isset($datum[$f]->transID)? array_push($statuses["failed"],$datum[$f]->transID) : array_push($statuses["failed"], "Nulus");
								//die("here");
							}
						}else{
							isset($datum[$f]->transID)? array_push($statuses["failed"],$datum[$f]->transID) : array_push($statuses["failed"], "Nulus");
							//die('Died here');
						}
						$f++;
					}
					$allUser = array();
					$allUser["Cashier"] = $cashier;
					$allUser['Guests'] = 0;
					$timestamp = strtotime(date('d-m-Y'));
					$st = "select*from transactions where transDate >= '$timestamp' and canteenID = '".$merchant."'";
					$jigga = new db();
					$jigga = $jigga->connect();
					$fi = $jigga->prepare($st);
					$fi->execute();
					$row = $fi->fetchAll();
					if($row){
						$allUser["Total-Transactions"] = count($row);
						$data = json_encode($row, true);
						$data = json_decode($data,true);
						for($i = 0; $i < count($data); $i++){
							$ut = $data[$i]['userType'];
							if(array_key_exists($ut, $allUser)){
								$allUser[$ut] = $allUser[$ut] + 1;
							}else{
								$allUser[$ut] = 1;
							}
						}
					}else{
						$allUser["Total-Transactions"] = 0;
						//$aMount = 0;
					}
					$st = "select count (*) as totalGuest from guests where transDate >= '$timestamp' and canteenID = '".$merchant."'";
					$fi = $jigga->prepare($st);
					$fi->execute();
					$row = $fi->fetchAll();
					if($row){
						$data = json_encode($row, true);
						$data = json_decode($data,true);
						$allUser["Guests"] = $data[0]['totalGuest'];
					}
					$allUser = json_encode($allUser);
					$statuses = json_encode($statuses);
					$sql = "SELECT canteen.settled as settled, canteen.unsettled as unsettled from cashier left join canteen on canteen.merchantID = cashier.merchantID where cashier.cashierID = '$cashierID' and canteen.merchantID = '$merchant'";
					$db = new db();
					$data = $db->selectFromQuery($sql);
					$data = substr($data, 1, count($data)-2);
					$g = '{"error":{"message":"","status":"0"}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$statuses.', "cashierData":'.$data.', "FeedStat":'.$allUser.'}}';
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
		$g = '{"error":{"message":"Cashier username, publicKey and merchantID is required", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			//->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/health/g/rep', function(Request $req, Response $resp){
	$href = $req->getParam('href');
	if(isset($href)){
		$ex = explode("/",$href);
		if(count($ex) == 6){
			$math = ($ex[3] * $ex[5]) / ($ex[2] * $ex[0]);
			if($math == ($ex[1] * $ex[4])){
				$sql = "select*from transactions where synced < 1 or synced is NULL  limit 10";
				$db = new db();
				$data = $db->selectFromQuery($sql);
				$data = str_replace('null','0',$data);
				$g = '{"error":{"message":"","status":"0"}, "content":{"data":'.$data.'}}';
			}else{
				$g = '{"error":{"message":"","status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"Something went wrong","status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"href not found","status":"1"}}';
	}
	$g = str_rot13($g);
	return $resp->withStatus(200)
	->withHeader('Content-Type', 'application/json')
	//->withHeader('Access-Control-Allow-Origin', '*')
	->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
	->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	->write($g);
});
$app->post('/api/health/r/rep', function(Request $req, Response $resp){
	$href = $req->getParam('href');
	$data = $req->getParam('data');
	if(isset($href)){
		$ex = explode("/",$href);
		if(count($ex) == 6){
			$math = ($ex[3] * $ex[5]) / ($ex[2] * $ex[0]);
			if($math == ($ex[1] * $ex[4])){
				$data = str_rot13($data);
				$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[], "Internal-Failure"=>[]);
				$transactions = json_decode($data);
				if($transactions->error->status == 0){
					$transCount = count($transactions->content->data);
					$datum = $transactions->content->data;
					$f = 0;
					$date = time();
					$imDB = new db();
					$imDB = $imDB->connect();
					$dbn = new db();
					while($f < $transCount){
						$sql = "SELECT * FROM transactions where transID = '".$datum[$f]->transID."' and cardSerial = '".$datum[$f]->cardSerial."'";
						if(!$dbn->isExist($sql)){
							$q = "insert into transactions (canteenID, cashier, amount, syncDate, cardSerial, transDate, transID, userType) values (:merchant, :cashier, :amount, :syncDate, :cardSerial, :transDate, :transID, :ut)";
							$fa = $imDB->prepare($q);
							$fa->bindValue(':merchant', $datum[$f]->canteenID);
							$fa->bindValue(':cashier', $datum[$f]->cashier);
							$fa->bindValue(':amount', $datum[$f]->amount);
							$fa->bindValue(':syncDate', $date);
							$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
							$fa->bindValue(':transDate', $datum[$f]->transdate);
							$fa->bindValue(':transID', $datum[$f]->transID);
							$fa->bindValue(':ut', $datum[$f]->userType);
							$fa->execute();
							array_push($statuses["successful"], $datum[$f]->transID);
							$fa->execute();
						}else{
							array_push($statuses["successful"], $datum[$f]->transID);
						}
						$f++;
					}
					$statuses = json_encode($statuses);
					$g = '{"error":{"message":"","status":"0"}, "content":{"data":'.$statuses.'}}';
				}else{
					$g = '{"error":{"message":"Error","status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Error","status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"","status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"","status":"1"}}';
	}
	$g = str_rot13($g);
	return $resp->withStatus(200)
	->withHeader('Content-Type', 'application/json')
	//->withHeader('Access-Control-Allow-Origin', '*')
	->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
	->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	->write($g);
});

$app->post('/api/health/g2/rep', function(Request $req, Response $resp){
	$href = $req->getParam('href');
	$data = $req->getParam('data');
	if(isset($href)){
		$ex = explode("/",$href);
		if(count($ex) == 6){
			$math = ($ex[3] * $ex[5]) / ($ex[2] * $ex[0]);
			if($math == ($ex[1] * $ex[4])){
				$data = str_rot13($data);
				$transactions = json_decode($data);
				if($transactions->error->status == 0){
					$transCount = count($transactions->content->data->successful);
					$datum = $transactions->content->data->successful;
					$f = 0;
					$date = time();
					$imDB = new db();
					$imDB = $imDB->connect();
					$dbn = new db();
					while($f < $transCount){
						$q = "update transactions set synced = 1 where transID = :id";
						$fa = $imDB->prepare($q);
						$fa->bindValue(':id', $datum[$f]);
						$fa->execute();
						$f++;
					}
					$g = '{}';
				}else{
					$g = '{"error":{"message":"","status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"","status":"1"}}';
			}
		}else{
			$g = '{"error":{"message":"","status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"","status":"1"}}';
	}
	$g = str_rot13($g);
	return $resp->withStatus(200)
	->withHeader('Content-Type', 'application/json')
	//->withHeader('Access-Control-Allow-Origin', '*')
	->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
	->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	->write($g);
});

$app->get('/api/install/update/{data}', function(Request $req, Response $resp){
	$sql = "ALTER TABLE admin  ADD `canteenID`	TEXT DEFAULT NULL";
	$sql2 = "ALTER TABLE cards	ADD `limitReached`	INTEGER DEFAULT 0";
	$sql3 = "ALTER TABLE cards ADD `synced`	INTEGER DEFAULT 0";
	try{
		$db = new db();
		$db = $db->connect();
		try{
			$f = $db->prepare($sql);
			$f->execute();
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
		try{
			$f = $db->prepare($sql2);
			$f->execute();
		}catch(PDOException $ex){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
		$f = $db->prepare($sql3);
		$f->execute();
		$g = '{"m":"eG"}';
	}catch(PDOException $ex){
		$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
	}
	//$g = str_rot13($g);
	return $resp->withStatus(200)
	->withHeader('Content-Type', 'application/json')
	//->withHeader('Access-Control-Allow-Origin', '*')
	->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
	->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
	->write($g);
});
?>