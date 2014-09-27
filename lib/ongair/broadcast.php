<?php
	
	require_once 'whatsprot.class.php';

	$username = $argv[1];
	$password = $argv[2];
	$nickname = $argv[3];
	$identity = strtolower(sha1($username, false));
	$method = $argv[5];
	$args = $argv[6];
	$targets = $argv[7];
	$externalId = $argv[8];

	echo "Username: ".$username."\r\n";
	echo "Password: ".$password."\r\n";
	echo "Nickname: ".$nickname."\r\n";
	echo "Identity: ".$identity."\r\n";
	echo "Method: ".$method."\r\n";
	echo "Args: ".$args."\r\n";
	echo "Targets:".$targets."\r\n";

	function onSyncResult($result)
	{
		// echo "Result ".$result."\r\n";
	    foreach($result->existing as $number)
	    {
	    	echo "Number ".$number." exists \r\n";
	    }

	    foreach($result->nonExisting as $number) {
	    	echo "Number ".$number." does not exist\r\n";
	    }

	    exit(0);
	}
	

	$w = new WhatsProt($username, $identity, $nickname, true);
	$w->connect();
	echo "Logging in";
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
		$res = $w->sendBroadcastImage($targets, $args, $externalId, false);
		
		// $w->eventManager()->bind('onMediaMessageSent', 'onMediaMessageSent');
	}
	elseif ($method == "sendBroadcastAudio") {
		$targets = explode(",", $targets);
		$w->sendBroadcastAudio($targets, $args, false);					
	}
	elseif ($method == "sendSync") {
		$targets = explode(",", $targets);
		echo "Logging in";

		$w->eventManager()->bind('onGetSyncResult', 'onSyncResult');
		$w->sendSync($targets);

		while(true) {
			$w->pollMessages(false);
		}
	}
	
	sleep(30);
	exit(0);
?>