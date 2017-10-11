<?php
require_once('self/server/config.php');
require_once('self/server/functions.php');
$states = redef('q','select*from states where id = 31 or id = 39 order by id asc', $jr, 0);
$states2 = redef('q','select*from states order by id asc', $jr, 0);
$lgs = redef('q','select*from localgovernment where stateID =  31', $jr, 0);
$inst = redef('q','select*from institution order by id asc', $jr, 0);
$invited = false;
if(isset($_GET['invite']) and is_numeric($_GET['invite'])){
	$invited = true;
}
if(isset($_POST['firstname']) and isset($_POST['lastname'])){
	$totalAmount = $_POST['amountDue'];
	$hostelName = $_POST['hostelName'];
	$roomName = $_POST['roomName'];
	$roomConfig = $_POST['config'];
	$xtraInfo = $_POST['comment'];
	$invitation = $_POST['invitationEmail'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$gender = $_POST['gender'];
	$birthday = $_POST['birthday'];
	$profilePic = $_FILES['profilePic']['name'];
	$phone = $_POST['phone'];
	$state = $_POST['state'];
	$LG = $_POST['LG'];
	$address = $_POST['address'];
	$institution = $_POST['institution'];
	$course = $_POST['course'];
	$courseDuration = $_POST['courseDuration'];
	$level = $_POST['level'];
	$program = $_POST['program'];
	$email = $_POST['email'];
	$username = $email;//$_POST['username'];
	$password = $phone;//$_POST['password'];
	$passwordAgain = $phone;
	$matric = $_POST['matric'];
	$invitations = explode(",",$invitation);
	if(!isset($totalAmount) or $totalAmount < 1){
		$message = eM('Sorry, Your hostel configuration is invalid!');
		return;
	}elseif($roomConfig == 2 and count($invitations) < 1){
		$message = eM('You have to Input at least an email to reserve bedspace for a proxy!');
		return;
	}
	if($roomConfig == 1 or $roomConfig == 3){
		$invitation = "";
	}
	if((strlen($firstname) >= 3 and validatestr($firstname,"str")) and strlen($lastname) >= 3){
		if(isset($birthday)){
			if(isset($phone) and strlen($phone) == 11 and validatestr($phone,"num")){
				if(isset($address)){
					if(isset($email) and validatestr($email, "mail")){
						if(strlen($username) >= 4){
							if(strlen($password) >= 6){
								if($password  == $passwordAgain){
									if(isset($_FILES['profilePic']['name'])){
										$fullname = $firstname." ".$lastname;
										$allowtype = array('jpg', 'jpeg','png');
										$form = strtolower($profilePic);
										$format = explode(".", $form);
										$type = end($format);
										if(in_array($type,$allowtype)){
											$s = getimagesize($_FILES['profilePic']['tmp_name']);
											if($s[0] >= 100 and $s[1] >= 100){
												if($_FILES['profilePic']['error'] == 0){
													if($s[0] < $s[1]){
														$maxsize = $s[1];
													}else{
														$maxsize = $s[0];
													}
													if($maxsize > 400){
														$redRat = 400/$maxsize;
													}else{
														$redRat = 1;
													}
													if(!file_exists("self/profileImages/".Mcrypt(time()))){
														mkdir("self/profileImages/".Mcrypt(time()));
													}else{
														//The username exists: unlikely though
													}
													$folder = "self/profileImages/".Mcrypt(time());
													$name = "S8_".time().".".$type;
													//$thumbname = "thumb_".$name;
													$tfile = $folder."/".$name;
													$thumbname = "thumb_".$name;
													$data = array("amountDue"=> $totalAmount,	"hostelName"=>$hostelName,	"roomName"=> $roomName,	"config"=>$roomConfig, "comment"=>$xtraInfo, "invitationEmail"=>$invitation, "username"=>$username, "email"=>$email, "password"=>$password, "fullname"=>$fullname, "gender"=>$gender, "birthday"=>$birthday, "profile"=>$tfile, "phone"=>$phone, "address"=>$address, "state"=>$state, "LG"=>$LG, "inst"=>$institution, "course"=>$course, "duration"=>$courseDuration, "matric"=>$matric, "level"=>$level, "pType"=>$program, "phone"=>$phone);
														$url = $base.'X/public/api/user/create/profile';
														$options = array(
																		 'http' => array(
																		 'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
																		 'method'  => 'POST',
																		 'content' => http_build_query($data)
																		  ));
														$context  = stream_context_create($options);
														$result = file_get_contents($url, true, $context);
														if ($result === FALSE) { /* Handle error */ }
														//var_dump($result);
														$data = json_decode($result);
														if($data->error->status > 0){
															$status = eM($data->error->message);
														}else{
															if(move_uploaded_file($_FILES["profilePic"]["tmp_name"], $tfile)){
															}
															resize_crop_image(round($s[0]*$redRat), round($s[1]*$redRat), $tfile, $tfile);
															thumbnail($thumbname,$name,$folder.'/',$folder.'/',50,50);
															$status = sM("Your Reservation has been succesful!");
															if($roomConfig == 2){
																$mess = "Thank You! <br/> Your hostel Reservation was succesfully booked. Please check your email for further instructions. Also encourage your friend to complete thier profile using the invite code sent to their email.";
															}else{
																$mess = "Thank You! <br/> Your hostel Reservation was succesfully booked. Please check your email for further instructions.";
															}
														}
													}else{
														$status = eM("The Image has errors! Use another image!");
														//image has errors
													}
												}else{
													$status = eM("The Image has size errors! Use image with bigger size!");
													//Image has size errors
												}
											}else{
												$status = eM("The Image format is unknown! Use another image!");
												//The Image is unknown type
											}
									}else{
										$status = eM("The Image field have not been set! Use another image!");
										//profile picture not set
									}
								}else{
									$status = eM("The Password fields do not match!");
									//password do not match
								}
							}else{
								$status = eM("The Password field expect at least 6 characters!");
								//password Length
							}
						}else{
							$status = eM("The username field expect at least 4 characters!");
							//username invalid
						}
					}else{
						$status = eM("The email is invalid. Use a valid email!");
						//email invalid
					}
				}else{
					$status = eM("The address field is invalid!");
					//address invalid
				}
			}else{
				$status = eM("The phone number field expect 11 characters Number only!");
				//Phone invalid
			}
		}else{
			$status = eM("The Bithday field is required!");
			//bithday incorrect
		}
	}else{
		$status = eM(" The Name fields are not properly filled out! ");
		///firstname Error
	}	
}
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
        <!-- title -->
        <title>Accommodate | Registeration Form</title>
        <meta name="author" content="touchandpay">
        <!-- favicon -->
        <link rel="shortcut icon" href="images/icon/favicon.png">
        <!-- animation -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
    
        
        
        
        
        
        <!-- font-awesome icon -->
        <link rel="stylesheet" href="o/css/font-awesome.min.css" />
        <!-- themify-icons -->
        <link rel="stylesheet" href="o/css/themify-icons.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="o/css/owl.transitions.css" />
        <link rel="stylesheet" href="o/css/owl.carousel.css" /> 
        <!-- magnific popup -->
        <link rel="stylesheet" href="o/css/magnific-popup.css" /> 
        <!-- base -->
        <link rel="stylesheet" href="o/css/base.css" /> 
        <!-- elements -->
        <link rel="stylesheet" href="o/css/elements.css" />
        <!-- responsive -->
        <link rel="stylesheet" href="o/css/responsive.css" />
        <link rel="stylesheet" href="self/style/governor.css" />
        
        
        
        <!--[if IE 9]>
        <link rel="stylesheet" type="text/css" href="o/css/ie.css" />
        <![endif]-->
        <!--[if IE]>
            <script src="o/js/html5shiv.min.js"></script>
        <![endif]-->
        
    </head>
    <body>
    <div id="stow" align="center"></div>
        
  
<?php
require_once('self/server/header.php');
?>

<div class="container sm-no-padding" style="margin-bottom:10px;
    margin-top: 10px;
    padding-bottom: 50px;
    padding-right: 100px;
    padding-left: 100px; background:#FFF; border-bottom:#C3C3C3 thin solid; box-shadow: 0 5px 15px rgba(0,0,0,0.5); border-radius:5px;
">
<div class="modal-header">
					<div align="center"><h4>Book <span>Now</span></h4></div>
					<img src="images/book-now.jpg" alt=" " class="img-responsive">
					<h5 style="padding:10px 0px 10px 0px">Register to create account</h5>
				</div>
<form  action="register<?php echo ($invited)? '?invite='.$_GET['invite'] : '' ?>" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="board">
            <ul class="nav nav-tabs">
                <div class="liner"></div>
                <li rel-index="0" class="active">
                    <a href="#step-1" id="step1" class="btn" aria-controls="step-1" role="tab" data-toggle="tab">
                        <span><i class="fa fa-bed"></i></span>
                    </a>
                </li>
                <li rel-index="1" class="">
                    <a href="#step-2" id="step2" class="btn" aria-controls="step-2" role="tab" data-toggle="tab">
                        <span><i class="glyphicon glyphicon-user"></i></span>
                    </a>
                </li>
                <li rel-index="2" class="">
                    <a href="#step-3" id="step3" class="btn" aria-controls="step-3" role="tab" data-toggle="tab">
                        <span><i class="fa fa-graduation-cap"></i></span>
                    </a>
                </li>
                <!-- <li rel-index="2">
                    <a href="#step-3" class="btn disabled" aria-controls="step-3" role="tab" data-toggle="tab">
                        <span><i class="glyphicon glyphicon-edit"></i></span>
                    </a>
                </li> -->
                <li rel-index="3">
                    <a href="#step-4" id="step4" class="btn" aria-controls="step-4" role="tab" data-toggle="tab">
                        <span><i class="glyphicon glyphicon-file"></i></span>
                    </a>
                </li>
                 <?php
            if(isset($mess)){?>
                <li rel-index="4">
                    <a href="#step-5" id="step5" class="btn green" aria-controls="step-5" role="tab" data-toggle="tab">
                    <?php
			}else{?>
				<li rel-index="4">
                    <a href="#step-5" id="step5" class="btn disabled" aria-controls="step-5" role="tab" data-toggle="tab">
			<?php }
			?>
			
                        <span><i class="glyphicon glyphicon-ok"></i></span>
                    </a>
                </li>                
            </ul>
        </div>
        <div class="tab-content">
        
        
        
        <!--
        	This is the choose hostel tab
         -->
        
        <?php
            if(isset($mess)){?>
        <div role="tabpanel" class="tab-pane" id="step-1">
        <?php }else{ ?>
        <div role="tabpanel" class="tab-pane active" id="step-1">
        <?php
		}
		?>
                <div class="col-md-12">
                	<div class="row">
                    	<div class=""><h4 style="padding-bottom:10px">Hostel by location</h4></div>
                        <div class="col-sm-6" style="height:auto;">
                            <div class="form-group" style="height:70px; z-index:45000">
                                <label for="first-name">Select State</label><br>
                                <?php
								if($invited){
									?>
                                    <input type="hidden" id="inviteCode" id="ivCode" value="<?php echo $_GET['invite'] ?>" />
                                    <?php
								}
								?>
                                <select class="form-control" style="z-index:4500000000" name="HostelState" id="hostelState" required onChange="matchList(this, 2)">
                                <?php
								  while($ip = redef('f',$states,$jr,0)){ ?>
								  <option value="<?php echo $ip['id']; ?>"><?php echo $ip['label']; ?></option>
								  <?php
								  }
								  ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group" style="height:50px">
                              <label for="last-name" id="area">Choose Area</label><br>
                              <select class="form-control" name="schoolLG" id="schoolLGs" required onChange="listHostel(this)">
                                  <?php
                                  while($ip = redef('f',$lgs,$jr,0)){ ?>
                                  <option value="<?php echo $ip['id']; ?>"><?php echo $ip['label']; ?></option>
                                  <?php
                                  }
                                  ?>
                              </select>
                          </div>
                        </div>
                        <div class="" style="padding-bottom:10px; padding-top:90px; color:#304f82" align="center"><h4 style="color:#304f82">Select Hostel</h4></div>
                        <div class="col-sm-6">
                         <div class="form-group">
                                <label for="height" id="chooseHostel">Choose Hostel</label><br>
                                <select class="form-control" name="hostelName" id="hostelName" onChange="populateRooms(this.selectedIndex)"></select>
                            </div>
                         </div>
                        <div class="col-sm-6">
				             <div class="form-group">
				                    <label for="weight">Rooms Type</label><br>
				                    <select class="form-control" name="roomName" id="Rooms" onChange="findTotal()">
					                	
					                </select>
				            </div>
                        </div>
                        <div class="" style="padding-bottom:10px; padding-top:90px; color:#304f82" align="center"><h4 style="color:#304f82">Configure Room</h4></div>     
                        <div class="col-sm-6">
                          <div class="form-group">
                              <label for="birth-city">Choose Room-mate options</label><br>
                              <select class="form-control" name="config" id="config" onChange="findTotal()">
                                  <option value="1">I want the room to myself</option>
                                  <option value="2">I Will provide my room mate(s)</option>
                                  <option value="3">I only want a bedspace</option>
                              </select>
                          </div>
                        </div>
                        <div class="col-sm-6" id="optionalParam">
                          <div class="form-group">
                              <label for="birth-country">Send Room mate Invitation to </label><br>
                              <input type="text" name="invitationEmail" id="OptComment" class="form form-control" disabled placeholder="Separate multiple email with comma">
                          </div>
                        </div>
                        <p></p>
                        <div class="col-sm-11" id="optionalComment" style="padding-top:20px; max-height:100px">
                          <div class="form-group" align="center" style="max-height:80px">
                              <label for="birth-country">Optional Comment </label><br>
                              <textarea type="text" id="" name="comment" class="form form-control" style="max-width:70%; max-height:60" placeholder="Write optional comment on your booking"></textarea>
                          </div>
                        </div>
                       
                        <div class="col-sm-11" align="center" style="padding:20px">
                          <div class="form-group">
                           <input type="hidden" name="amountDue" id="amountDue" value="">
                            <hr/>
                              <h5>Total Amount Due: <span id="totalAmount"></span></h5>
                          </div>
                        </div>
                        
                	</div>  
                
                    <div class="col-md-12 text-center">
                        <button id="step-1-next"  class="btn btn-primary write nextBtn input-medium text-center" style="border-radius:0px; width: 100px; background-color: #304F82; border-color: #304F82; margin-top: 20px;">Next</button>
                    </div> 
              </div>
         </div>
        
        
        
        <!--
        	This the the personal Info tab
         -->
        
            <div role="tabpanel" class="tab-pane" id="step-2">
                <div class="col-md-12"> 
                <h4 style="padding-bottom:10px; padding-top:10px">Personal Information</h4>
                                <div class="col-md-6" style="height:auto">
	            				<div class="form-group" style="height:70px">
				                    <label for="first-name">First Name:</label><br>
				                    <input type="text" name="firstname" value="<?php echo isset($_POST['firstname'])? $_POST['firstname'] : '' ?>" class="form-control" id="firstname" required style="padding-bottom:8px;">
				                </div>
                                </div>
                                
                                <div class="col-md-6">
				                <div class="form-group" style="height:70px">
				                    <label for="last-name">Last Names:</label><br>
				                    <input type="text" name="lastname" value="<?php echo isset($_POST['lastname'])? $_POST['lastname'] : '' ?>" class="form-control" id="lastname" required style="padding-bottom:8px;">
				                </div>
                                </div>
                                
                                <div class="col-md-6">
				                <div class="form-group">
				                    <label for="height">Gender:</label><br>
				                    <select class="form-control" name="gender" id="gender">
					                	<option value="Male">Male</option>
					                	<option value="Female" <?php echo isset($_POST['gender']) and ($_POST['gender']== "Female")? "selected='selected'" : "" ?>>Female</option>
					                </select>
				                </div>
                                </div>
                                
                                <div class="col-md-6">
				                <div class="form-group">
				                    <label for="weight">Birthday (DD/MM/YYYY):</label><br>
				                    <input type="date" name="birthday" value="<?php echo isset($_POST['birthday'])? $_POST['birthday'] : '' ?>" class="weight form-control" id="birthday" placeholder="dd/mm/yy">
				                </div>
                                </div>
                                
                                <div class="col-md-11" style="padding-bottom:8px; padding-top:8px; height:70px">
                                <input type="file" name="profilePic" id="profilePic" style="display:none" onChange="document.getElementById('profileIm').innerHTML = 'Image Name :'+this.value">
	            				<button type="file" class="btn btn-info" style="background:#304f82; border-radius:0px; border:none;" onClick="document.getElementById('profilePic').click()"><i class="fa fa-image"></i> Upload Passport Picture</button> <span id="profileIm" style="font-weight:bold"></span>
	            			</div>
                            
                          <hr style="padding:12px" />
	            				<div class="col-sm-11" align="center"><h4 style="padding-top:20px; padding-bottom:10px; color:#304f82">Other Information</h4></div>                                
                                <div class="col-md-6">
	            				<div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="birth-city">Phone No:</label><br>
				                    <input type="number" value="<?php echo isset($_POST['phone'])? $_POST['phone'] : '' ?>" min="1" name="phone" maxlength="11" class="telephone form-control" id="phone-no" required>
				                </div>
                                </div>
                                
                                <div class="col-md-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="birth-country">State / Province of Origin:</label><br>
				                    <select class="form-control" name="state" id="state" required onChange="matchList(this)">
                                    <?php
										while($ip2 = redef('f',$states2,$jr,0)){ ?>
					                	<option value="<?php echo $ip2['id']; ?>"><?php echo $ip2['label']; ?></option>
                                        <?php
										}
										?>
					                </select>
				                </div>
                                </div>
                                
                                <div class="col-md-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="birth-date" id="lg">Local Government of Origin:</label><br>
				                    <select class="form-control" name="LG" id="LGs" required>
					                	<?php
										while($ip = redef('f',$lgs,$jr,0)){ ?>
					                	<option value="<?php echo $ip['id']; ?>"><?php echo $ip['label']; ?></option>
                                        <?php
										}
										?>
					                </select>
				                </div>
                                </div>
                                <div class="col-md-6">
                                 <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="birth-state">Address:</label><br>
				                    <input type="text" value="<?php echo isset($_POST['address'])? $_POST['address'] : '' ?>" name="address"  class="address form-control" id="address" style=""  required>
				                </div>
                                </div>
                                <div class="col-sm-6">
           						 <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="address-city">Email:</label><br>
				                    <input type="email" name="email" id="emal" value="<?php echo isset($_POST['email'])? $_POST['email'] : '' ?>" class="address-city form-control" required>
				                </div>
                                </div>
                
                    <div class="col-md-11 text-center">
                        <button id="step-2-next" class="btn btn-primary write input-medium text-center pull-right" style="border-radius:0px; width: 100px; background-color: #304F82; border-color: #304F82; margin-top: 20px; margin-bottom: 30px;">Next</button>
                    <button id="step-1-next" onClick="validate(1)" class="btn btn-primary bluenew input-medium text-center pull-left" style="border-radius:0px; width: 100px; background-color: transparent; border-color: #304F82; margin-top: 20px;">Back</button>
                    </div>
               </div>
          </div>
            
            
            
            
            
            <!-- 
           		Institution tab
            -->
            
            <div role="tabpanel" class="tab-pane" id="step-3">
            <div class="col-sm-12"><h4>Instituition Information</h4></div>
            <div class="col-sm-6">
            <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
                                  <label for="institution">Instituition:</label><br>
                                  <select class="form-control" name="institution" required id="institution">
                                  <?php
                                      while($ip = redef('f',$inst,$jr,0)){ ?>
                                      <option value="<?php echo $ip['id']; ?>"><?php echo $ip['schoolName']; ?></option>
                                      <?php
                                      }
                                      ?>
                                  </select>
                              </div>
                              </div>
                                
                                <div class="col-sm-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="address-city">Course:</label><br>
				                    <input type="text" name="course" value="<?php echo isset($_POST['course'])? $_POST['course'] : '' ?>" class="address-city form-control" id="course" required>
				                </div>
                                </div>
                                <div class="col-sm-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="address-state">Course Duration:</label><br>
				                    <input type="number" name="courseDuration" min="1" value="<?php echo isset($_POST['courseDuration'])? $_POST['courseDuration'] : '' ?>" class="address-state form-control" id="course-duration" required>
				                </div>
                                </div>
                                <div class="col-sm-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="address-country">Matric Number:</label><br>
				                    <input type="text" name="matric" value="<?php echo isset($_POST['matric'])? $_POST['matric'] : '' ?>" class="address-country form-control" id="matric-number" required>
				                </div>
                                </div>
                                <div class="col-sm-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="address-postal-code">Level / Current year:</label><br>
				                    <input type="number" min="1" name="level" value="<?php echo isset($_POST['level'])? $_POST['level'] : '' ?>" class="address-postal-code form-control" id="level" required>
				                </div>
                                </div>
                                <div class="col-sm-6">
				                <div class="form-group" style="padding-bottom:8px; padding-top:8px; height:70px">
				                    <label for="telephone">Programme:</label><br>
				                    <select class="form-control" name="program" required id="progType">
					                	<option value="Post Graduate">Post Graduate</option>
					                	<option value="Under Graduate" <?php echo isset($_POST['program']) and $_POST['program']== "Under Graduate"? "selected='selected'" : "" ?>>Under Graduate</option>
					                </select>
				                </div>
                                </div>
                    
                    
                    <button id="step-3-next" class="btn btn-primary write input-medium text-center pull-right" style="border-radius:0px; width: 100px; background-color: #304F82; border-color: #304F82; margin-top: 20px; margin-bottom: 30px;">Next</button>
                    <button id="step-2-next" class="btn btn-primary bluenew input-medium text-center pull-left" style="border-radius:0px; width: 100px; background-color: transparent; border-color: #304F82; margin-top: 20px;">Back</button>
               
            </div>
            
              
            
            <!--
            	Preview page
             -->           
            
            <div role="tabpanel" class="tab-pane" id="step-4">
            <div class="col-sm-12">
            <h4>Preview your application </h4>
            </div>
            	<div class="col-sm-12" style="padding-left:30px; padding-top:20px; padding-bottom:15px; font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif"><h6>Hostel Booking Details</h6>
                	<div class="row">
                        <div class="col-lg-6" style="font-size:16px; padding-top:10px; word-break:break-word">
                            <span style="font-weight:bold" id="hosteln">Hostel Name: </span><br/>
                            <span id="hostela"></span>
                        </div>
                        <div class="col-lg-6" style="font-size:16px; padding-top:10px">
                            <span style="font-weight:bold">Room Name: <span id="roomn"> </span></span>
                            <br/>
                            <span style="font-weight:bold">Room Type: <span id="roomt"></span></span>
                        </div>
                        <div class="col-lg-6" style="font-size:16px; padding-top:10px">
                            <span style="font-weight:bold">Configuration: <span id="conf"></span></span>                            
                        </div>
                        <div class="col-lg-6" style="font-size:16px; padding-top:10px">
                            <h6 style="font-weight:bold">Total Price: <span id="pr"></span></h6>                            
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-12" style="padding-left:30px; padding-top:20px; font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif"><h6>Personal Information Details</h6>
                	<div class="row" style="font-size:16px; padding-top:10px">
                    	<div class="col-md-6">Fullname: <span id="fns"></span></div>
                        <div class="col-md-6">Gender: <span id="sOg"></span></div>
                        <div class="col-md-6">Birthday: <span id="bd"></span></div>
                        <div class="col-md-6">Phone Number: <span id="pn"></span></div>
                        <div class="col-md-6">Location: <span id="loc"></span></div>
                    </div>
                </div>
                <div class="col-sm-12" style="padding-left:30px; padding-top:20px; font-family:'Palatino Linotype', 'Book Antiqua', Palatino, serif"><h6>Institution Information Details</h6>
                	<div class="row" style="font-size:16px; padding-top:10px">
                    	<div class="col-md-6">Institution: <span id="inst"></span></div>
                        <div class="col-md-6">Course: <span id="cus"></span></div>
                        <div class="col-md-6">Course-Duration: <span id="cdur"></span></div>
                        <div class="col-md-6">Matric: <span id="mtr"></span></div>
                        <div class="col-md-6">Current Year: <span id="cy"></span></div>
                        <div class="col-md-6">Programme: <span id="pgT"></span></div>
                    </div>
                </div>
  
            
            
            <hr style="padding:10px" />
            
                <div class="col-md-2" align="right" style="padding-top:7px; margin-right:-77px">
                <div align="right"><input type="checkbox" name="termsCheck" required id="termsCheck"></div>
                </div>
                <div class="col-md-4" align="left" style="padding:0px">
                <div align="left"><div class="btn btn-link" data-toggle="modal" data-target="#form-wizard" style="border-radius:0px;">I Accept Terms and Condition</div></div>
                </div>
                
                  <div class="col-md-6">                                       
                    <button type="submit" class="btn btn-primary write input-medium text-center pull-right" style="border-radius:0px; width: 100px; background-color:#304F82; border-color: #304F82; margin-top: 20px; margin-bottom: 30px; margin-right: 10px;">Submit</button>
                </div>
            </div>
            
            
            
            
            
            
            <!--
            	The Consent 
             -->
             <?php
            if(isset($mess)){
				echo '<div role="tabpanel" class="tab-pane active" id="step-5">';
			}else{
				echo '<div role="tabpanel" class="tab-pane" id="step-5">';
			}
			?>
            <div class="col-sm-12">
            <div align="center" style="padding:17px"><i class="fa fa-check-circle fa-2x"></i><h5 style="color:#3D8D16">
            
			<?php
            if(isset($mess)){
				echo $mess;
			}
			?>
			</h5></div>
                </div>
            </div>
            <!--
            	The end of content
            -->          
            
            
            
            
        </div>
        
        
    </div></form>
</div>
        
        
        
        <div class="row" style="background:#1c1c1c; padding-top:20px; max-width:101%;">
		<?php
		require_once('self/server/footer.php');
		?>
        </div> 
        <?php
	require_once('self/server/modals.php');
	?>

        <!-- javascript libraries -->
        <script type="text/javascript" src="o/js/jquery.min.js"></script>
        <script type="text/javascript" src="o/js/jquery.appear.js"></script>
        <script type="text/javascript" src="o/js/smooth-scroll.js"></script>
        <script type="text/javascript" src="o/js/bootstrap.min.js"></script>
        <!-- wow animation -->
        <script type="text/javascript" src="o/js/wow.min.js"></script>
        <!-- owl carousel -->
        <script type="text/javascript" src="o/js/owl.carousel.min.js"></script>        
        <!-- images loaded -->
        <script type="text/javascript" src="o/js/imagesloaded.pkgd.min.js"></script>
        <!-- isotope -->
        <script type="text/javascript" src="o/js/jquery.isotope.min.js"></script> 
        <!-- magnific popup -->
        <script type="text/javascript" src="o/js/jquery.magnific-popup.min.js"></script>
        <!-- navigation -->
        <script type="text/javascript" src="o/js/jquery.nav.js"></script>
        <!-- equalize -->
        <script type="text/javascript" src="o/js/equalize.min.js"></script>
        <!-- fit videos -->
        <script type="text/javascript" src="o/js/jquery.fitvids.js"></script>
        <!-- number counter -->
        <script type="text/javascript" src="o/js/jquery.countTo.js"></script>
        <!-- time counter  -->
        <script type="text/javascript" src="o/js/counter.js"></script>
        <!-- twitter Fetcher  -->
        <script type="text/javascript" src="o/js/twitterFetcher_min.js"></script>
        <!-- main -->
        <script type="text/javascript" src="o/js/main.js"></script>
        <script>
                        $(function() {
    // Nav Tab stuff
		$('.nav-tabs > li > a').click(function() {
			if($(this).hasClass('disabled')) {
				return false;
			} else {
				var linkIndex = $(this).parent().index() - 1;
				isValidated = validatePage(linkIndex);
				if(isValidated){
					$('.nav-tabs > li').each(function(index, item) {
						$(item).attr('rel-index', index - linkIndex);
					});
				}else{
					return false;
				}
			}
		});
		$('#step-1-next').click(function() {
			// Check values here
			var isValid = validatePage(0);			
			if(isValid) {
				$('.nav-tabs > li:nth-of-type(2) > a').removeClass('disabled').click();
			}
		});
		$('#step-2-next').click(function() {
			// Check values here
			var isValid = validatePage(1);		
			if(isValid) {
				$('.nav-tabs > li:nth-of-type(3) > a').removeClass('disabled').click();
			}
		});
		$('#step-3-next').click(function() {
			// Check values here
			var isValid = validatePage(2);		
			if(isValid) {
				$('.nav-tabs > li:nth-of-type(4) > a').removeClass('disabled').click();
			}
		});
		$('#step-4-next').click(function() {
			// Check values here
			var isValid = validatePage(3);			
			if(isValid) {
				$('.nav-tabs > li:nth-of-type(4) > a').removeClass('disabled').click();
			}
		});
		$('#step-5-next').click(function() {
			// Check values here
			var isValid = true;			
			if(isValid) {
				$('.nav-tabs > li:nth-of-type(4) > a').removeClass('disabled').click();
			}
		});
	});
			
			
			
        </script>        
        <script type="text/javascript" src="self/script/s.js"></script>
        <script type="text/javascript">
        <?php
		if(isset($status)){
			echo "mCra('".str_replace("'","",$status)."')";
		}?>
    </script>
    </body>
</html>