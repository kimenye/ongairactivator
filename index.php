<?php
	require_once('lib/limonade.php');
	require_once('lib/ongair/whatsprot.class.php');

	date_default_timezone_set('Africa/Nairobi');

	// $syncResult = [];
	// $pollMessages = true;
	// $GLOBALS['syncResult'] = [];
	// $GLOBALS['pollMessages'] = true;


	function onSyncResult($result)
	{
		$syncResult = [];
		// echo "Result ".$result."\r\n";
	    foreach($result->existing as $number)
	    {
	    	// echo "Number ".$number." exists \r\n";
	    	array_push($syncResult, $number);
	    }

	    // foreach($result->nonExisting as $number) {
	    	// echo "Number ".$number." does not exist\r\n";
	    // }

	    // array_push($GLOBALS['syncResult'], "254705866564");


	    // $pollMessages = false;
	    // exit(0);
	    // $GLOBALS['pollMessages'] = false;
	    return json(array( "status" => true, "registered" => $syncResult ));
	}

	dispatch_post('/', 'home');
	function home() {
		$method = $_POST['method'];
		$jid = $_POST['jid'];

		if ($method == "identity") {			
			$identity = strtolower(sha1($jid, false));
			// $identity = createIdentity($jid);
			
			return json(array( "identity" => $identity, "db" => getenv('DB') ));
		}
		elseif ($method == "request") {
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];

			$w = new WhatsProt($jid, $identity, $nickname, false);
			$result = $w->codeRequest('sms');
			
			# code...

			return json(array( "jid" => $jid, "result" => $result));
		}
		elseif ($method == "register") {
		
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$code = $_POST['code'];
			$test = $_POST['test'];

			
			$password = "";
			$test = "no";
			

			if ($test == "yes" || $test == "true")
			{
				// $password = "10weOLsovhm9M5HXCcvJRvcLJeY=";
			}
			else {
				$w = new WhatsProt($jid, $identity, $nickname, false);
				$result = $w->codeRegister($code);
				$password = $result->pw;			
			}

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
			// return json(array( "status" => $result, "image" => $image_url ));			
			
			return json(array ( "success" => true, "file" => realpath('tmp/profile.jpg') ));
		}
		elseif ($method == "setStatusMessage") {
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
		elseif ($method == "broadcastImage") {
			# code...
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$password = $_POST['password'];
			$contacts = $_POST['contacts'];
			$image = $_POST['image'];

			$w = new WhatsProt($jid, $identity, $nickname, true);
			$w->connect();
			$w->loginWithPassword($password);

			$targets = explode(",", $contacts);
			$result = $w->sendBroadcastImage($targets, $image, false);

			sleep(5);
			return json(array( "status" => $result, "image" => $image, "targets" => $targets ));

		}
		elseif ($method == "broadcastMessage") {			
			# code...
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$password = $_POST['password'];
			$contacts = $_POST['contacts'];
			$message = $_POST['message'];

			$w = new WhatsProt($jid, $identity, $nickname, true);
			$w->connect();
			$w->loginWithPassword($password);

			$targets = explode(",", $contacts);
			$result = $w->sendBroadcastMessage($targets, $message);

			sleep(5);
			return json(array( "status" => $result, "message" => $message, "targets" => $targets ));
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
				$w->pollMessages();
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
