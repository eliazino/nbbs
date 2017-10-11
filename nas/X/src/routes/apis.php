<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;

/*
//	Admin Engine
*/

// Create Admin profile
$app->post('/api/admin/create/profile', function(Request $req, Response $resp){
	$adminUser = $req->getParam('adminUser');
	$adminKey = $req->getParam('adminKey');
	$username = $req->getParam('username');
	$fullname = $req->getParam('fullname');
	$email = $req->getParam('email');
	$pass = $req->getParam('password');
	//$imageDir = $req->getParam('profileImage');
	$phone = $req->getParam('phone');
    //$adminKey = $req->getParam('adminKey');
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$pass.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$mcrypto = new mcrypt();
	$publicKey = $mcrypto->mCryptThis(time());
	$dbn = new db();
	try{
		$ut = array('admin',444);
        if($dbn->verifyKey($adminUser, $adminKey, $ut)){
            $dbn = new db();
			$check = "select * from adminprofile where email = '$email'";
			$chk2 = "select * from adminprofile where username = '$username'";
			if($dbn->isExist($check)){
				$g = $g = '{"error":{"message":"Sorry, the email exists", "status":"1"}}';
			}else if($dbn->isExist($chk2)){
				$g = $g = '{"error":{"message":"Sorry, the username exists", "status":"1"}}';
			}else{
				$dbn = null;
				$dbn = new db();
				$dbn = $dbn->connect();
				$stmt = $dbn->prepare("INSERT INTO adminprofile (username,email,fullname, password,phone,publicKey) VALUES (?,?,?,?,?,?)");
				$stmt->bindParam(1, $username);
				$stmt->bindParam(2, $email);
				$stmt->bindParam(3, $fullname);
				$stmt->bindParam(4, $encryptedPassword);
				$stmt->bindParam(5, $phone);
				$stmt->bindParam(6, $publicKey);
				$stmt->execute();
				$g = '{"error":{"message":"", "status":"0"},"success":{"message":"user created","status":"200"}, "content":{"publicKey":"'.$publicKey.'", "username":"'.$username.'"}}';
			}
        }else{
			$g = $g = '{"error":{"message":"Sorry, Access Issues with "'.$adminUser.'", "status":"1"}}';
		}
	}catch(PDOException $e){
		$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
	}
	return $resp->withStatus(200)
       		->withHeader('Content-Type', 'application/json')
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
			$sql = "select count(*) from adminprofile where username='".$username."' and password='".$encryptedPassword."'";
			$dbn = $dbn->connect();
			$qr = $dbn->query($sql);
			if ($qr->fetchColumn() > 0) {
				$sql = "update adminprofile set sessionKey = '".$publicKey."', lastseen = '".$lastseen."' where username = '".$username."'";
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
       		->write($g);
});

/*
//		Admin Engine Ends
*/


/*
//	Student Engine
*/

$app->post('/api/user/create/profile', function(Request $req, Response $resp){
	$totalAmount = $req->getParam('amountDue');
	$hostelName = $req->getParam('hostelName');
	$roomName = $req->getParam('roomName');
	$roomConfig = $req->getParam('config');
	$xtraInfo = $req->getParam('comment');
	$invitation = trim($req->getParam('invitationEmail'));
	$username = $req->getParam('username');
	$email = $req->getParam('email');
	$pass = $req->getParam('password');
	$fullname = $req->getParam('fullname');
	$gender = $req->getParam('gender');
	$birthDay = $req->getParam('birthday');
	$profilePix = $req->getParam('profile');
	$phone = $req->getParam('phone');
	$address = $req->getParam('address');
	$state = $req->getParam('state');
	$LG = $req->getParam('LG');
	$inst = $req->getParam('inst');
	$course = $req->getParam('course');
	$duration = $req->getParam('duration');
	$matric = $req->getParam('matric');
	$level = $req->getParam('level');
	$pType = $req->getParam('pType');
	$phone = $req->getParam('phone');
	$thisTime = time();
	$key = ' FitSKchgoHOOKing666';
	$string = $key.'34iIlm'.$pass.'io9m-';
	$encryptedPassword = hash('sha256', $string);
	$mcrypto = new mcrypt();
	$publicKey = $mcrypto->mCryptThis(time());
	$verifyKey = time();
	$verifyKey = hash('sha256', $verifyKey);
	$verified = 0;
	$locked = 0;
	$expiry = time() + 12000000000;
	$skip = true;
	if($roomConfig == 2){
		 if(strlen($invitation) >= 5){
			 $stripI = explode(',',$invitation);
			 if(count($stripI) > 0){

			 }else{
				 $g = '{"error":{"message":"Sorry, You have to provide emails of the invited users for reservations", "status":"1"}}';
				 //error here
				 $skip = false;
			 }
		 }else{
			 //error here
			 $g = '{"error":{"message":"Sorry, You have to provide emails of the invited users  for reservations", "status":"1"}}';
			 $skip = false;
		 }
	}
	try{
        if($skip){
            $dbn = new db();
			$check = "select * from studentprofile where email = '$email'";
			$chk2 = "select * from studentprofile where username = '$username'";
			if($dbn->isExist($check)){
				$g = '{"error":{"message":"Sorry, the email exists", "status":"1"}}';
			}else if($dbn->isExist($chk2)){
				$g = '{"error":{"message":"Sorry, the username exists", "status":"1"}}';
			}else{
				$dbn = null;
				$dbn = new db();
				$dbn = $dbn->connect();
				$stmt = $dbn->prepare("INSERT INTO studentprofile (username, email, fullname, password, sessionKey, gender, birthdate, phones, originState, originLG, address, institution, course, courseDuration, matricNumber, currentYear, profilePicture, verified, locked) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$stmt->bindParam(1, $username);
				$stmt->bindParam(2, $email);
				$stmt->bindParam(3, $fullname);
				$stmt->bindParam(4, $encryptedPassword);
				//$stmt->bindParam(5, $phone);
				$stmt->bindParam(5, $publicKey);
				$stmt->bindParam(6, $gender);
				$stmt->bindParam(7, $birthDay);
				$stmt->bindParam(8, $phone);
				$stmt->bindParam(9, $state);
				$stmt->bindParam(10, $LG);
				$stmt->bindParam(11, $address);
				$stmt->bindParam(12, $inst);
				$stmt->bindParam(13, $course);
				$stmt->bindParam(14, $duration);
				$stmt->bindParam(15, $matric);
				$stmt->bindParam(16, $level);
				$stmt->bindParam(17, $profilePix);
				$stmt->bindParam(18, $verified);
				$stmt->bindParam(19, $locked);
				$stmt->execute();
				$dbn = null;
				$dbn = new db();
				$dbn = $dbn->connect();
				$mip = "INSERT INTO `booking` (`userID`, `roomID`, `bookingCat`, `amountDue`, `hostelID`, `bookingDate`, `inInvite`, `comment`) VALUES (:email, :room, :bookingcat, :amount, :hostel, :bookingdate, :invite, :comment)";
				$st3 = $dbn->prepare($mip);
				$st3->bindParam(':email', $email);
				$st3->bindParam(':room',$roomName);
				$st3->bindParam(':amount',$totalAmount);
				$st3->bindParam(':hostel',$hostelName);
				$st3->bindParam('bookingdate',$thisTime);
				$st3->bindParam('bookingcat',$roomConfig);
				$st3->bindParam(':invite',$roomConfig);
				$st3->bindParam(':comment', $xtraInfo);
				$st3->execute();
				$sql2 = "insert into verifyemail (username, email, VerifyKey, expiry) values (:user, :email, :verifyKey, :expiry)";
				$st2 = $dbn->prepare($sql2);
				$st2->bindParam(":user",$username);
				$st2->bindParam(":email",$email);
				$st2->bindParam(":verifyKey",$verifyKey);
				$st2->bindParam(":expiry",$expiry);
				$st2->execute();
				if($roomConfig == 2){
					for($i = 0; $i < count($stripI); $i++){
						$r = $dbn->prepare("insert into invites (email, inviteCode, inviter, hostelID, roomID, inviteDate) values (:mail, :code, :inviter, :hostelID, :room, :date)");
						$invite = time()*rand(100,90099);
						$r->bindParam(':mail', $stripI[$i]);
						$r->bindParam(':code', $invite);
						$r->bindParam(':inviter', $email);
						$r->bindParam(':hostelID', $hostelName);
						$r->bindParam(':room', $roomName);
						$r->bindParam(':date', $thisTime);
						$r->execute();
						$subject = "StudentAccommod8 Reservation";
						$customMessage = "We are pleased to inform you that a room reservation by ".$fullname." has been succesfull. log on to <a href='http://www.studentaccommod8.com/n/new/register?invite=".$invite."'>Registration Page</a> using your invite code '".$invite."' and your email to continue your registration";
						$dbn = new db();
						$dbn->sendThis($stripI[$i], array($customMessage,$subject));
					}
				}
				$subject = "Welcome to StudentAccommod8";
				$customMessage = "We are pleased to inform you that your registration hass been succesful and your booking has been logged. You will be contacted very soon via this email. <br> Please verify your email soon to Validate your registration. <br> <a href='http://www.studentaccommod8.com/verify/".$verifyKey."'>Verify your email now </a> or copy this link to browser 'http://www.studentaccommod8.com/verify/".$verifyKey."'";
				$dbn = new db();
				$dbn->sendThis($email, array($customMessage,$subject));
				$g = '{"error":{"message":"", "status":"0"},"success":{"message":"user created","status":"200"}, "content":{"publicKey":"'.$hostelName.'", "username":"'.$roomName.'"}}';
			}
        }
	}catch(PDOException $e){
		$g = '{"error":{"message":"'.$e->getMessage().'", "status":"1"}}';
	}
	return $resp->withStatus(200)
       		->withHeader('Content-Type', 'application/json')
      		->write($g);
});


//Log in student
$app->post('/api/user/login', function(Request $req, Response $resp){
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
			$sql = "select count(*) from studentprofile where username='".$username."' and password='".$encryptedPassword."'";
			$dbn = $dbn->connect();
			$qr = $dbn->query($sql);
			if ($qr->fetchColumn() > 0) {
				$sql = "update studentprofile set publicKey = '".$publicKey."', lastseen = '".$lastseen."' where username = '".$username."'";
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
       		->write($g);
});

/*
//	Student Engine ends
*/



/*
	General User Engine

*/

$app->post('/api/any/contactUs', function(Request $req, Response $resp){
	$fullname = $req->getParam('fullname');
	$email = $req->getParam('email');
	$message = $req->getParam('message');
	$time = time();
	if(isset($fullname) and isset($email) and isset($message)){
		$sql = "insert into contactUS (email, sentDate, message, fullname) values (:email, :sentDate, :message, :fullname)";
		$dbn = new db();
		$dbn = $dbn->connect();
		$f = $dbn->prepare($sql);
		$f->bindParam(":email", $email);
		$f->bindParam(":sentDate", $time);
		$f->bindParam(":message", $message);
		$f->bindParam(":fullname", $fullname);
		$f->execute();
		$g = '{"error":{"message":"", "status":"0"}, "success":{"message":"Your feedback has been logged! Response will be sent to your email", "status":"200"}, "content":{"publicKey":"'.$publicKey.'", "username":"'.$username.'"}}';
	}else{
		$g = '{"error":{"message":"All fields are required", "status": "1"}}';
	}
	return $resp->withStatus(200)
			->withHeader('Content-Type','application/json')
			->write($g);
});

$app->get('/api/any/get/hostel/{lg}', function(Request $req, Response $resp){
	$lg = $req->getAttribute('lg');
	
	$q = "select hostels.id as hostelID, address, hostelName, hostelState, bedSpacePrice, bedSpaces, rooms.id as roomID, occupants, roomDetails, roomName, roomNumber from hostels left join rooms on rooms.hostelID = hostels.id where hostels.hostelLG = ?";	
	$db = new db();
	$db = $db->connect();
	try{
		$stmn = $db->prepare($q);
		$stmn->execute(array($lg));
		$data = $stmn->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		$data = json_encode($data);
		$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
	}catch(PDOException $r){
		$g = '{"error":{"message":"All fields are required", "status": "1", "verbose":"'.$r->getMessage().'"}}';
	}
	return $resp->withStatus(200)
			->withHeader('Content-Type','application/json')
			->write($g);
});

/*
	General User Engine ends

*/

$app->post('/api/any/sendEmail',function(Request $req, Response $resp){
//die('yiiii');
		$email= $req->getParam('mail');
		$verifyKey = '123456789';
		$subject = "Welcome to StudentAccommod8";
		//die('yiiii');
				$customMessage = "We are pleased to inform you that your registration hass been succesful and your booking has been logged. You will be contacted very soon via this email. <br> Please verify your email soon to Validate your registration. <br> <a href='http://www.studentaccommod8.com/verify/".$verifyKey."'>Verify your email now </a> or copy this link to browser 'http://www.studentaccommod8.com/verify/".$verifyKey."'";
				$dbn = new db();				
				echo ($dbn->sendThis($email, array($customMessage,$subject)));
				
				/*{
				echo 'fine';
				}else{
				//$errorMessage = error_get_last()['message'];
				echo $errorMessage;
				}*/
				
});

$app->get('/api/user/get/inviteDetails/{invite}', function(Request $req, Response $resp){
	$i = $req->getAttribute('invite');
	$q = "SELECT roomName, roomNumber, roomDetails, bedSpacePrice, bedSpaces, occupants, inviteCode, inviteID, inviteEmail, inviter, hid, rid, address, hostelName, hostelLG, hostelState, lg, states.label as state from  (SELECT roomName, roomNumber, roomDetails, bedSpacePrice, bedSpaces, occupants, inviteCode, inviteID, inviteEmail, inviter, hid, rid, address, hostelName, hostelLG, hostelState, localgovernment.label as lg from (SELECT roomName, roomNumber, roomDetails, bedSpacePrice, bedSpaces, occupants, inviteCode, inviteID, inviteEmail, inviter, hid, rid, address, hostelName, hostelLG, hostelState from (SELECT rooms.roomName, rooms.roomNumber, rooms.roomDetails, rooms.bedSpacePrice, rooms.bedSpaces, rooms.occupants, inviteCode, invites.id as inviteID, invites.email as inviteEmail, inviter, invites.hostelID as hid, invites.roomID as rid, inviteDate  FROM `invites` left join rooms on rooms.id = invites.roomID where invites.inviteCode = ?) as Q1 left join hostels on hostels.id = Q1.hid) as Q2 left join localgovernment on localgovernment.id = hostelLG) as Q3 left join states on states.id = Q3.hostelState";
	$dbn = new db();
	$dbn = $dbn->connect();
	$st = $dbn->prepare($q);
	if ($st->execute(array($i))) {
		$data = json_encode($st->fetchAll());
		$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
	}else{
		$data = '{}';
		$g = '{"error":{"message":"","status":""}, "success":{"message":"data grabbed","code":"200"}, "content":{"data":'.$data.'}}';
	}
	return $resp->withStatus(200)
			->withHeader('Content-Type','application/json')
			->write($g);

});
?>