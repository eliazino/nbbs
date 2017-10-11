$app->post('/api/agent/sync/transactions', function(Request $req, Response $resp){
	$cashier = $req->getParam('username');
	$merchant = $req->getParam('publicKey');
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
									$q = "update cards set assigned = 0 where cardSerial = :cardSerial";
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