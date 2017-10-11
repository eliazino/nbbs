<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;
error_reporting(0);

$app->post('/api/sync/transactions', function(Request $req, Response $resp){
	$json = $req->getParsedBody();
	$json = isset($json)? $json : $req->getBody();
	$headers = $req->getHeaders();
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
	/*$username = $json->agentID;
	$key = $json->publicKey;
	$device = $json->deviceCode;*/
	$transactions = $json->data->transactions;
	if(true){//isset($username) and isset($key)){
		//$sql = "SELECT*FROM agent where agentID = '$username' and publicKey = '$key'";
		try{
			$dbn = new db();
			if(true){
				//$transactions = urldecode($transactions);
				if(true){//$dbn->isJson($transactions)){
					$imDB = new db();
					$imDB = $imDB->connect();
					$statuses = array("failed"=>[], "successful"=>[], "duplicate"=>[], "Internal-Failure"=>[]);
					//$transactions = json_decode($transactions);
					$transCount = count($transactions);
					$datum = $transactions;
					$f = 0;
					while($f < $transCount){
						if(isset($datum[$f]->transID) and isset($datum[$f]->amount)){							
							$a = "select * from cards where cardSerial = '".$datum[$f]->cardSerial."' and assigned = 1";
							if(true){//$dbn->isExist($a)){
								$datum[$f]->amount = (int)$datum[$f]->amount;
								$sql = "SELECT * FROM transactions where transID = '".$datum[$f]->transID."'";
								if(!$dbn->isExist($sql)){
									$u2 = "INSERT INTO transactions (truck, weightBefore, weightAfter, material, company, amountDue, transDate, paymentType, transID, cardSerial, driverName, driverPhone, truckType, gateFee, syncDate) VALUES (:truck, :weightBefore, :weightAfter, :material, :company, :amountDue, :transDate, :paymentType, :transID, :cardSerial, :driverName, :driverPhone, :truckType, :gateFee, :syncDate)";
									$syncDate = time();									
									$fa = $imDB->prepare($u2);
									$fa->bindValue(':truck', $datum[$f]->truckID);
									$fa->bindValue(':weightBefore', $datum[$f]->weightBefore);
									$fa->bindValue(':weightAfter', $datum[$f]->weightAfter);
									$fa->bindValue(':material', $datum[$f]->material);
									$fa->bindValue(':company', $datum[$f]->companyName);
									$fa->bindValue(':amountDue', $datum[$f]->amount);
									$fa->bindValue(':transDate', $datum[$f]->transDate);
									$fa->bindValue(':paymentType', $datum[$f]->paymentType);
									$fa->bindValue(':transID', $datum[$f]->transID);
									$fa->bindValue(':cardSerial', $datum[$f]->cardSerial);
									$fa->bindValue(':driverName', $datum[$f]->driverName);
									$fa->bindValue(':driverPhone', $datum[$f]->driverPhone);
									$fa->bindValue(':truckType', $datum[$f]->truckType);
									$fa->bindValue(':gateFee', $datum[$f]->gateFee);
									$fa->bindValue(':syncDate', $syncDate);
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
?>