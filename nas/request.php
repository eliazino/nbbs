<?php
session_start();
require_once('self/server/config.php');
require_once('self/server/functions.php');
if(isset($_SESSION['s8Ad']) and isset($_SESSION['s8Key'])){
	if(isset($_GET['download'])){
		$sql = redef("q","SELECT hostelName, fullname, roomDetails, occupants, hostelTable.address as addr, phones, email, gender, userID, roomID, bookingCat, amountDue, hid, bookingDate,comment from (SELECT fullname, roomDetails, occupants, address, phones, email, gender, userID, roomID, bookingCat, amountDue, hid, bookingDate,comment from (SELECT fullname, address, phones, email, gender, userID, roomID, bookingCat, amountDue, booking.hostelID as hid, bookingDate,comment FROM `booking` left join studentprofile on studentprofile.email = booking.userID) as roomTable left join rooms on rooms.id = roomTable.roomID) as hostelTable left join hostels on hostels.id = hostelTable.hid",$jr,0);
		$xml = new SimpleXMLElement('<xmlData/>');
		$mother = $xml->addChild('Requests');
		while($i = redef("fetch",$sql, $jr,0)){
			$track = $mother->addChild('RequestData');
			$track->addChild('Fullname', $i['fullname']);
			$track->addChild('email', $i['email']);
			$track->addChild('Phone', $i['phones']);
			$track->addChild('Address', $i['addr']);
			$track->addChild('Gender', $i['gender']);
			$track->addChild('HostelName', $i['hostelName']);
			$track->addChild('RoomDetails', $i['roomDetails']);
			$track->addChild('NoOfOccupants', $i['occupants']);
			if($i['bookingCat'] == 1){
				$txt = "I want the room to mysself";
			}elseif($i['bookingCat'] == 2){
				$txt = "I will provide my roomate(s)";
			}else{
				$txt = "I only want a bedSpace";
			}
			$track->addChild('Category', $txt);
			$track->addChild('Amount', $i['amountDue']);
			$track->addChild('Date', date("D-M-Y H:i A",$i['bookingDate']));
			//$track->addChild('Comments', $i['comment']);
		}
			header('Content-disposition: attachment; filename="newfile.xml"');
			header('Content-type: "text/xml"; charset="utf8"');
			//readfile('newfile.xml');
			print($xml->asXML());
			die();
			
	}
}else{
	if(isset($_POST['login'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$url = $base.'X/public/api/admin/login';
		if(isset($username) and isset($password)){
			$data = array('username' => $username, 'password' => $password);
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, true, $context);
			if ($result === FALSE) { /* Handle error */ }
			$data = json_decode($result);
			if($data->error->status > 0){
				$status = eM($data->error->message);
			}else{
				if(true){
					$_SESSION['s8Ad'] = $data->content->username;
					$_SESSION['s8Key'] = $data->content->publicKey;
				}else{
					$_SESSION['s8Ad'] = $data->content->username;
					$_SESSION['s8Key'] = $data->content->publicKey;
				}
				$status = sM("Account login succesful!");
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Requests</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,700">
    <link rel="stylesheet" href="assets2/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets2/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets2/css/form-elements.css">
    <link rel="stylesheet" href="assets2/css/style.css">
    <link rel="stylesheet" href="assets2/css/media-queries.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css"  />
</head>

<body>
<?php
if( !isset($_SESSION['s8Ad']) or !isset($_SESSION['s8Key'])){?>

	<div class="row">
    <div class="col-sm-4"></div>
    	<div class="col-sm-4" align="center">
        <?php echo isset($status)? $status : "" ?>
        <form name="log" action="request" method="post">
            <div style="padding-top:20px">
                <div class="form form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter username" class="form form-control" />
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form form-control" />
                </div>
                <div class="form-group"></div>
                    <button class="btn btn-primary" name="login"> Sign in</button>
                </div>
        </form>
        </div>
        <div class="col-sm-4"></div>
    </div>
<?php
}else{
	$a = redef("q","SELECT localgovernment.label as LG, fullname,address, phones, email, gender, originLG, profilePicture, schoolName, stateLabel from (SELECT fullName, phones, email, gender,address, originLG, profilePicture, schoolName, states.label as stateLabel FROM (select fullname, phones, email, address, gender, originState, originLG, profilePicture, schoolName from studentprofile left join institution on institution.id = studentprofile.institution order by studentprofile.id asc) as newTab LEFT JOIN states on newTab.originState = states.id) as leftTab left join localgovernment on localgovernment.id = leftTab.originLG",$jr,0);
?>
<div style="padding:12px; font-weight:bold" align="right"><a href="destroy">Logout</a> | <a href="request.php?download=true&t=<?php echo time() ?>">Download Sheet</a></div>
    <div class="row" style="padding:12px;">
    	<div class="col-sm-12">
        	<div class="table table-responsive">
        		<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Fullname</th>
                <th>State</th>
                <th>LG</th>
                <th>phone</th>
                <th>email</th>
                <th>School</th>
                <th>Address</th>
                <th>Gender</th>
                <th>Picture</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>S/N</th>
                <th>Fullname</th>
                <th>State</th>
                <th>LG</th>
                <th>phone</th>
                <th>email</th>
                <th>School</th>
                <th>Address</th>
                <th>Gender</th>
                <td>Picture</td>
            </tr>
        </tfoot>
        <tbody>
        <?php
		$count = 1;
		while($x = redef("fetch",$a, $jr,0)){ ?>
            <tr>
                <td><?php echo $count ?></td>
                <td><?php echo $x['fullname']; ?></td>
                <td><?php echo $x['stateLabel']; ?></td>
                <td><?php echo $x['LG']; ?></td>
                <td><?php echo $x['phones']; ?></td>
                <td><?php echo $x['email']; ?></td>
                <td><?php echo $x['schoolName']; ?></td>
                <td><?php echo $x['address']; ?></td>
                <td><?php echo $x['gender']; ?></td>
                <td><img src="<?php 
					$vb = explode("/",$x['profilePicture']);
					$vb[3] = "thumb_".$vb[3];
					echo implode("/",$vb);
				 ?>"  /></td>
            </tr>
            <?php
			$count++;
		}
		?>
        </tbody>
    </table>
        	</div>
        </div>
    </div>
    <?php
}
?>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="assets2/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</html>