<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;

$app->post('/api/admin/create/profile', function (Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$email = $req->getParam('email');
	$phone = $req->getParam('phone');
	$fullname = $req->getParam('fullname');
	if(isset($username) and isset($password) and isset($email) and isset($phone) and isset($fullname)){
		if(is_numeric($fullname)){
			if(strlen($password) > 6){
				$sql = "select*from admin where username = '$username'";
				$db = new db();
				try{
					if(!$db->isExist($sql)){
						$sql = "select*from admin where email = '$email'";
						if(!$db->isExist($sql)){
							$sql = "insert into admin (fullname, email, phone, password, username, publicKey) values (:fullname, :email, :phone, :passwod, :username, :key)";
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
							$f->execute();
							$f = new db();
							$data = $f->selectFromQuery("SELECT username, fullname, email, phone, lastseen FROM admin WHERE username = '$username' and publicKey = '$key'");
							$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"The Admin has been created","code":"200"}, "content":{"username":"'.$username.'", "publicKey":"'.$fkey.'", "data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
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
	if(isset($username) and isset($password) and isset($email) and isset($phone) and isset($fullname)){
		if(is_numeric($fullname)){
			if(strlen($password) > 6){
				$sql = "select*from admin where username = '$username'";
				$db = new db();
				try{
					if(!$db->isExist($sql)){
						$sql = "select*from admin where email = '$email'";
						if(!$db->isExist($sql)){
							$sql = "update admin set fullname = :fullname, email = :email, phone = :phone where username = :username and publicKey = :key ";
							$db = $db->connect();
							$f = $db->prepare($sql);
							$f->bindValue(':fullname',$fullname);
							$f->bindValue(':email', $email);
							$f->bindValue(':phone',$phone);
							$f->bindValue(':username', $username);
							$f->bindValue(':key', $key);
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
			->withHeader('Access-Control-Allow-Origin', '*')
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
			->withHeader('Access-Control-Allow-Origin', '*')
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
			$sql = "select count(*) from admin where username='".$username."' and password='".$encryptedPassword."'";
			$dbn = $dbn->connect();
			$qr = $dbn->query($sql);
			if ($qr->fetchColumn() > 0) {
				$sql = "update admin set publicKey = '".$publicKey."', lastseen = '".$lastseen."' where username = '".$username."'";
				$stmt = $dbn->prepare($sql);
			    $stmt->execute();
				$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Login successful", "status":"200"}, "content":{"publicKey":"'.$publicKey.'", "username":"'.$username.'"}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/admin/create/merchant', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$address = $req->getParam('address');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
	$bankName = $req->getParam('bankName');
	$accountName = $req->getParam('accountName');
	$accountNumber = $req->getParam('accountNumber');
	$referal = $req->getParam('referalID');
	$discount = $req->getParam('discount');
	$goldClubDiscount = $req->getParam('goldClubDiscount');
	$othersDiscount = $req->getParam('othersDiscount');
	$mcrypto = new mcrypt();
	$merchantID = $mcrypto->mCryptThis(time()/35);
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($address) and isset($phone) and isset($bankName) and isset($accountName) and isset($accountNumber) and isset($referal) and isset($discount) and isset($goldClubDiscount) and isset($othersDiscount) and isset($email)){
					if(is_numeric($phone)){
						if(is_numeric($accountNumber)){
							if(is_numeric($discount) and is_numeric($goldClubDiscount) and is_numeric($othersDiscount)){
								$q = "insert into merchants (name, address, accountNumber, phone, referalID, discount, goldClubDiscount, othersDiscount, bankName, accountName, merchantID, email) values (:name, :address, :accountnum, :phone, :refID, :discount, :goldCD, :othersD, :bank, :accountname, :merchantID, :email)";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':address', $address);
								$f->bindParam(':accountnum', $accountNumber);
								$f->bindParam(':phone', $phone);
								$f->bindParam(':refID', $referal);
								$f->bindParam(':discount', $discount);
								$f->bindParam(':goldCD', $goldClubDiscount);
								$f->bindParam(':othersD', $othersDiscount);
								$f->bindParam(':bank', $bankName);
								$f->bindParam(':accountname', $accountName);
								$f->bindParam(':merchantID', $merchantID);
								$f->bindParam(':email', $email);
								$f->execute();
								$f = new db();
								$data = $f->selectFromQuery("SELECT * FROM merchants");
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Merchant creation successful", "status":"200"}, "content":{"merchantID":"'.$merchantID.'", "Name":"'.$name.'", "data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->put('/api/admin/update/merchant', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$name = $req->getParam('name');
	$address = $req->getParam('address');
	$phone = $req->getParam('phone');
	$bankName = $req->getParam('bankName');
	$accountName = $req->getParam('accountName');
	$accountNumber = $req->getParam('accountNumber');
	$email = $req->getParam('email');
	$referal = $req->getParam('referalID');
	$discount = $req->getParam('discount');
	$goldClubDiscount = $req->getParam('goldClubDiscount');
	$othersDiscount = $req->getParam('othersDiscount');
	$merchantID = $req->getParam('merchantID');
	if(isset($username) and isset($key)){
		$sql = "select*from admin where username = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if(isset($name) and isset($address) and isset($phone) and isset($bankName) and isset($accountName) and isset($accountNumber) and isset($referal) and isset($discount) and isset($goldClubDiscount) and isset($othersDiscount) and isset($email)){
					if(is_numeric($phone)){
						if(is_numeric($accountNumber)){
							if(is_numeric($discount) and is_numeric($goldClubDiscount) and is_numeric($othersDiscount)){
								$q = "update merchants set name = :name, address = :address, accountNumber = :accountnum, phone = :phone, referalID = :refID, discount = :discount, goldClubDiscount = :goldCD, email = :email,  othersDiscount = :othersD, bankName = :bank, accountName = :accountname where merchantID = :merchantID";
								$dbn = $dbn->connect();
								$f = $dbn->prepare($q);
								$f->bindParam(':name', $name);
								$f->bindParam(':address', $address);
								$f->bindParam(':accountnum', $accountNumber);
								$f->bindParam(':phone', $phone);
								$f->bindParam(':refID', $referal);
								$f->bindParam(':discount', $discount);
								$f->bindParam(':goldCD', $goldClubDiscount);
								$f->bindParam(':othersD', $othersDiscount);
								$f->bindParam(':bank', $bankName);
								$f->bindParam(':accountname', $accountName);
								$f->bindParam(':merchantID', $merchantID);
								$f->bindParam(':email', $email);
								$f->execute();
								$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Merchant update was successful", "status":"200"}, "content":{"merchantID":"'.$merchantID.'", "Name":"'.$name.'"}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/admin/create/cashier', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$name = $req->getParam('fullname');
	$phone = $req->getParam('phone');
	$email = $req->getParam('email');
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
				if(isset($merchantID) and $dbn->isExist("select*from merchants where merchantID = '$merchantID'")){
					if(isset($name) and isset($phone) and isset($email)){
						if(is_numeric($phone)){
							if(!$dbn->isExist("select*from cashier where email = '$email' and merchantID = '$merchantID'")){
								if(!$dbn->isExist("select*from cashier where phone = '$phone' and merchantID = '$merchantID'")){
									$dbn = $dbn->connect();
									$sql = "INSERT INTO cashier (fullname, email, phone, password,  merchantID, username, addedBy, cashierID) VALUES (:fullname, :email, :phone, :password, :merchant, :username, :added, :cashier)";
									$f = $dbn->prepare($sql);
									$f->bindParam(':fullname', $name);
									$f->bindParam(':email', $email);
									$f->bindParam(':phone', $phone);
									$f->bindParam(':password', $encryptedPassword);
									$f->bindParam(':merchant', $merchantID);
									$f->bindParam(':username', $email);
									$f->bindParam(':added', $username);
									$f->bindParam(':cashier', $cashID);
									$f->execute();
									$f = new db();
									$data = $f->selectFromQuery("SELECT fullname, email, phone, merchantID, cashierID, username FROM cashier where merchantID = '$merchantID' order by id desc");
									$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"cashier was successfully added", "status":"200"}, "content":{"Username":"'.$email.'", "password":"'.$phone.'", "cashierID" : "'.$cashID.'", "data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->put('/api/admin/set/access/cashier/{to}', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$pkey = $req->getParam('publicKey');
	$to = $req->getAttribute('to');
	$cashier = $req->getParam('cashierID');
	if(isset($username) and isset($key)){
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/admin/set/dividend', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$key = $req->getParam('publicKey');
	$bpay = $req->getParam('beepPay');
	$t2p = $req->getParam('touch2pay');
	$ref = $req->getParam('referer');
	if(isset($username) and isset($key)){
		try{
			$dbn = new db();
			$sql = "select*from admin where username = '$username' and publicKey = '$key'";
			if($dbn->isExist($sql)){
				if(isset($bpay) and isset($t2p) and isset($ref)){
					$total = $t2p + $bpay + $ref;
					if($total == 100){
						$sql = "INSERT INTO dividend (beepPay, issuer, referer) VALUES (:bp, :issuer, :ref)";
						$dbn = $dbn->connect();
						$f = $dbn->prepare($sql);
						$f->bindParam(':bp', $bpay);
						$f->bindParam(':issuer', $t2p);
						$f->bindParam(':ref', $ref);
						$f->execute();
						$f = new db();
						$data = $f->selectFromQuery("SELECT * FROM dividend order by id desc limit 1");
						$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"New distribution has been set. Effective immediately", "status":"200"}, "content":{"data":'.$data.'}}';
					}else{
						$g = '{"error":{"message":"Total distribution cannot be greater or less than 100%. Try Again!", "status":"1"}}';
					}
				}else{
					$g = '{"error":{"message":"All Parameters are required", "status":"1"}}';
				}
			}else{
				$g = '{"error":{"message":"Validation Failed. Admin profile not found", "status":"1"}}';
			}
		}catch(PDOException $x){
			$g = '{"error":{"message":"'.$ex->getMessage().'", "status":"1"}}';
		}
	}else{
		$g = '{"error":{"message":"Validation credentials are not found", "status":"1"}}';
	}
	return $resp->withStatus(200)
      		->withHeader('Content-Type', 'application/json')
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->get('/api/admin/get/merchants/{searchParam}', function (Request $req, Response $resp){
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
						$q = "SELECT*FROM merchants order by id desc";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT*FROM merchants where name like :name order by id desc";
						$f = $db->prepare($q);
						$f->execute(array(':name' => '%'.$search.'%'));
					}
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->get('/api/admin/get/cashier/{merchantID}', function (Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute('merchantID');
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT * FROM admin";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					if($search == "-"){
						$q = "SELECT fullname, email, phone, merchantID, cashierID, username FROM cashier order by id desc";
						$f = $db->prepare($q);
						$f->execute();
					}else{
						$q = "SELECT fullname, email, phone, merchantID, cashierID, username FROM cashier where merchantID = :name order by id desc";
						$f = $db->prepare($q);
						$f->execute(array(':name' => $search));
					}
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
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
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					$q = "SELECT username, fullname, email, phone, lastseen FROM admin WHERE username = :username and publicKey = :key";
					$f = $db->prepare($q);
					$f->execute(array(':key' => $publicKey, ':username' => $username));
					$row = $f->fetch();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->get('/api/admin/get/transactions/{merchant}', function(Request $req, Response $resp){
	$headers = $req->getHeaders();
	$search = $req->getAttribute("merchant");
    if (array_key_exists('HTTP_PUBLICKEY', $headers) and array_key_exists('HTTP_USERNAME', $headers)) {
        $publicKey  = $headers['HTTP_PUBLICKEY'][0];
        $username  = $headers['HTTP_USERNAME'][0];
		$q = "SELECT*FROM admin WHERE username = '$username' and publicKey = '$publicKey'";
		try{
			$dbn = new db();
			if($dbn->isExist($q)){
				if(isset($search)){
					$db = $dbn->connect();
					if($search == "-") $ssq = ""; else $ssq = "where merchantID = :merchant";
					$q = "SELECT transactions.id as transactionID, merchants.name as merchantName, cashier, amount, merchants.discount as merchantDiscount, issuerValue, refererValue, beepPayValue, syncDate, cardBalance, cardSerial FROM transactions left join merchants on merchants.merchantID = transactions.merchant ".$ssq;
					$f = $db->prepare($q);
					if($ssq == "") $f->execute(); else $f->execute(array(':merchant' => $search));
					$row = $f->fetchAll();
					if($row){
						$data = json_encode($row, true);
					}else{
						$data = "[]";
					}
					$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});

$app->post('/api/cashier/login', function(Request $req, Response $resp){
	$username = $req->getParam('username');
	$password = $req->getParam('password');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$password.'io9m-';
	$password = hash('sha256', $string);
	if(isset($username) and isset ($password)){
		$sql = "SELECT fullname, cashier.email, cashier.phone, cashier.merchantID, cashier.cashierID, cashier.username, merchants.discount, merchants.goldClubDiscount, merchants.othersDiscount, merchants.Name as merchantName from cashier left join merchants on merchants.merchantID = cashier.merchantID where cashier.username = :username and cashier.password = :pass";
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
				$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
$app->post('/api/cashier/sync/cardDetails', function(Request $req, Response $resp){

});
$app->post('/api/cashier/sync/transactions', function(Request $req, Response $resp){
	$cashier = $req->getParam('username');
	$cashierID = $req->getParam('cashierID');
	$merchant = $req->getParam('merchantID');
	$transactions = $req->getParam('transactions');
	$date = time();
	if(isset($cashier) and isset($cashierID) and isset($merchant)){
		$sql = "SELECT*FROM cashier where username = '$cashier' and cashierID = '$cashierID' and merchantID = '$merchant'";
		try{
			$dbn = new db();
			if($dbn->isExist($sql)){
				if($dbn->isJson($transactions)){
					$imDB = new db();
					$imDB = $imDB->connect();
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[]);
					$transactions = json_decode($transactions);
					$transCount = count($transactions->data);
					$datum = $transactions->data;
					$batchID = time()."/".substr($cashier, 0,3)."/".substr($merchant,0,3)."/".rand(10000,100000000);
					$requirements = $dbn->getAllDiscount($merchant);
					$f = 0;
					$merchantMoney = 0;
					$refererMoney = 0;
					$issuerMoney = 0;
					$beepMagnetMoney = 0;
					while($f < $transCount){
						if(isset($datum[$f]->amount) and strlen($datum[$f]->amount) > 0 and isset($datum[$f]->cardBalance) and strlen($datum[$f]->cardBalance) > 0 and isset($datum[$f]->cardSerial) and strlen($datum[$f]->cardSerial) > 0 and isset($datum[$f]->transDetails) and strlen($datum[$f]->transDetails) > 0 and isset($datum[$f]->transDate) and strlen($datum[$f]->transDate) > 0 and isset($datum[$f]->cardType) and strlen($datum[$f]->cardType) > 0 and isset($datum[$f]->transID) and strlen($datum[$f]->transID) > 0){
							$sql = "SELECT * FROM transactions where transID = '".$datum[$f]->transID."' and cardSerial = '".$datum[$f]->cardSerial."' and merchant = '".$merchant."'";
							if(!$dbn->isExist($sql)){
								$q = "insert into transactions (merchant, cashier, amount, discountType, discount, issuerValue, refererValue, beepPayValue, syncDate, cardBalance, cardSerial, transDate, transDetails, transID, batchID, charge, merchantValue) values (:merchant, :cashier, :amount, :discountType, :discount, :issuerValue, :refererValue, :beepPayValue, :syncDate, :cardBalance, :cardSerial, :transDate, :transDetails, :transID, :batchID, :charge, :merchantValue)";
								$tmoney = floatval($datum[$f]->amount)*(100 - floatval($requirements["discounts"][0]))/100;								

								$charge = floatval($datum[$f]->amount)*(100 - floatval($requirements["discounts"][$datum[$f]->cardType]))/100;
								$remainder =  -$tmoney + $charge;
								$merchantMoney = $tmoney + $merchantMoney;

								$refM = $remainder*floatval($requirements["sharePerc"][2])/100;
								$refererMoney = $refM + $refererMoney;

								$issuerM = $remainder*floatval($requirements["sharePerc"][0])/100;
								$issuerMoney = $issuerM + $issuerMoney;

								$beepMo = $remainder*floatval($requirements["sharePerc"][1])/100;
								$beepMagnetMoney = $beepMo + $beepMagnetMoney;
								$fa = $imDB->prepare($q);
								$fa->bindValue(':merchant', $merchant);
								$fa->bindValue(':cashier', $cashier);
								$fa->bindValue(':amount', $datum[$f]->amount);
								$fa->bindValue(':discountType', $datum[$f]->cardType);
								$fa->bindValue(':discount', $requirements["discounts"][0]);
								$fa->bindValue(':issuerValue', $issuerM);
								$fa->bindValue(':refererValue', $refM);
								$fa->bindValue(':beepPayValue', $beepMo);
								$fa->bindValue(':syncDate', $date);
								$fa->bindValue(':cardBalance', $datum[$f]->cardBalance);
								$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
								$fa->bindValue(':transDate', $datum[$f]->transDate);
								$fa->bindValue(':transDetails', $datum[$f]->transDetails);
								$fa->bindValue(':transID', $datum[$f]->transID);
								$fa->bindValue(':batchID', $batchID);
								$fa->bindValue(':charge', $charge);
								$fa->bindValue(':merchantValue', $tmoney);
								$fa->execute();
								array_push($statuses["successful"], $datum[$f]->transID);
							}else{
								array_push($statuses["duplicate"], $datum[$f]->transID);
							}
						}else{
							isset($datum[$f]->transID)? array_push($statuses["failed"],$datum[$f]->transID) : array_push($statuses["failed"], "Nulus");
						}
						$f++;
					}
					if($merchantMoney > 0){
						$wsq = "INSERT INTO transBatch (batchID, merchant, issuervalue, beepPayValue, refererValue, syncDate, merchantValue) VALUES (:batchID, :merchant, :issuerValue, :beepPayValue, :refererValue, :syncDate, :merchantValue)";
						$fa = $imDB->prepare($wsq);
						$fa->bindParam(":batchID", $batchID);
						$fa->bindParam(":merchant", $merchant);
						$fa->bindParam(":issuerValue", $issuerMoney);
						$fa->bindParam(":beepPayValue", $beepMagnetMoney);
						$fa->bindParam(":refererValue", $refererMoney);
						$fa->bindParam(":syncDate", $date);
						$fa->bindParam(":merchantValue", $merchantMoney);
						$fa->execute();
					}
					$statuses = json_encode($statuses);
					$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$statuses.'}}';
				}else{
					$g = '{"error":{"message":"The transaction is not a valid JSON. Array of transactions is Expecetd", "status":"1"}}';
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
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
		    ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))
       		->write($g);
});
?>