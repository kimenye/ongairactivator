<?php
	require_once('lib/limonade.php');
	require_once('lib/ongair/whatsprot.class.php');

	date_default_timezone_set('Africa/Nairobi');

	// $syncResult = [];
	// $pollMessages = true;
	// $GLOBALS['syncResult'] = [];
	// $GLOBALS['pollMessages'] = true;


	// function onSyncResult($result)
	// {
	// 	$syncResult = [];
	// 	// echo "Result ".$result."\r\n";
	//     foreach($result->existing as $number)
	//     {
	//     	// echo "Number ".$number." exists \r\n";
	//     	array_push($syncResult, $number);
	//     }

	//     // foreach($result->nonExisting as $number) {
	//     	// echo "Number ".$number." does not exist\r\n";
	//     // }

	//     // array_push($GLOBALS['syncResult'], "254705866564");


	//     // $pollMessages = false;
	//     // exit(0);
	//     // $GLOBALS['pollMessages'] = false;
	//     return json(array( "status" => true, "registered" => $syncResult ));
	// }

	dispatch_post('/', 'home');
	function home() {
		$method = $_POST['method'];
		$jid = $_POST['jid'];

		if ($method == "identity") {			
			$identity = strtolower(sha1($jid, false));			
			return json(array( "identity" => $identity ));
		}		
		elseif ($method == "register") {		
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$code = $_POST['code'];			
						
			$w = new WhatsProt($jid, $identity, $nickname, false);
			$result = $w->codeRegister($code);
			$password = $result->pw;						

			return json(array( "identity" => $identity, "jid" => $jid, "test" => $test, "code" => $code, "password" => $password ));
		}
		elseif ($method == "setProfilePicture") {
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$password = $_POST['password'];
			$image_url = $_POST['image_url'];

			$ch = curl_init($image_url);
			$fp = fopen('tmp/profile.jpg', 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			$w = new WhatsProt($jid, $identity, $nickname, false);
			$w->connect();
			$w->loginWithPassword($password);

			$result = $w->sendSetProfilePicture(realpath('tmp/profile.jpg'));

			sleep(5);
									
			return json(array ( "success" => true ));
		}
		elseif ($method == "profile_setStatus") {
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$password = $_POST['password'];
			$status = $_POST['status'];

			$w = new WhatsProt($jid, $identity, $nickname, false);
			$w->connect();
			$w->loginWithPassword($password);

			$w->sendStatusUpdate($status);

			sleep(5);

			
			return json(array ( "success" => true ));
		}		
		elseif ($method == "broadcast_Image") {
			# code...
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$password = $_POST['password'];
			$contacts = $_POST['targets'];
			$image = $_POST['image'];
			$externalId = $_POST['externalId'];

			$w = new WhatsProt($jid, $identity, $nickname, false);
			$w->connect();
			$w->loginWithPassword($password);

			$targets = explode(",", $contacts);
			$result = $w->sendBroadcastImage($targets, $image, $externalId, false);

			sleep(10);
			return json(array( "status" => null, "image" => $image, "targets" => $targets, "externalId" =>  $externalId ));

		}		
		elseif ($method == "sendSync") {
			# code...
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$password = $_POST['password'];
			$contacts = $_POST['contacts'];

			$targets = explode(",", $contacts);

			$w = new WhatsProt($jid, $identity, $nickname, true);
			$w->connect();
			$w->loginWithPassword($password);

			$w->eventManager()->bind('onGetSyncResult', 'onSyncResult');
			

			while(true) {				
				$w->pollMessages(false);
			}
			// return json(array( "status" => true, "registered" => $GLOBALS['syncResult'], "id" => $identity ));

		}

		return html('home/index.html.php'); # rendering HTML view
	}

	function createIdentity($username) {
		return strtolower(sha1($username, false));
	}

	run();
?>
