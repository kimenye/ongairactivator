<?php
	
	require_once 'whatsprot.class.php';

	$username = $argv[1];
	$password = $argv[2];
	$nickname = $argv[3];
	$identity = $argv[4];
	$method = $argv[5];
	$args = $argv[6];
	$targets = $argv[7];

	// echo "Username: ".$username."\r\n";
	// echo "Password: ".$password."\r\n";
	// echo "Nickname: ".$nickname."\r\n";
	// echo "Identity: ".$identity."\r\n";
	// echo "Method: ".$method."\r\n";
	// echo "Args: ".$args."\r\n";
	// echo "Targets:".$targets."\r\n";

	function onSyncResult($result)
	{
	    foreach($result->existing as $number)
	    {
	        // echo "$number exists<br />";
	        // echo $number;
			$r = new HttpRequest($args, HttpRequest::METH_POST);
			$r->addPostFields(array('jid' => $number));

			try {
			    echo $r->send()->getBody();
			    die();
			} catch (HttpException $ex) {
			    echo $ex;
			}
	    }
	}

	$w = new WhatsProt($username, $identity, $nickname, true);
	$w->connect();
	$w->loginWithPassword($password);

	if ($method == "sendStatusUpdate") {
		// echo "About to send status update.\r\n";
		$w->sendStatusUpdate($args);
	}
	elseif ($method == "sendProfilePicture") {
		// echo "About to send profile picture.\r\n";
		$w->sendSetProfilePicture($args);
	}
	elseif ($method == "broadcastMessage") {
		$targets = explode(",", $targets);
		// echo "About to broadcast a message.\r\n".print_r($targets)."\r\n";
		$w->sendBroadcastMessage($targets, $args);
	}
	elseif ($method == "sendImage") {
		// $targets = explode(",", $targets);
		$w->sendMessageImage($targets, $args, false);
	}
	elseif ($method == "sendBroadcast") {
		$targets = explode(",", $targets);
		$w->sendBroadcastMessage($targets, $args);
	}
	elseif ($method == "sendBroadcastImage") {
		$targets = explode(",", $targets);
		$w->sendBroadcastImage($targets, $args, false);
	}
	elseif ($method == "sendBroadcastAudio") {
		$targets = explode(",", $targets);
		$w->sendBroadcastAudio($targets, $args, false);	
	}
	elseif ($method == "syncContacts") {
		$w->eventManager()->bind('onGetSyncResult', 'onSyncResult');
		$w->sendSync(array($targets));

		while(true) {
			$w->pollMessages();
		}
	}
	
	sleep(5);
	exit(0);
?>