<?php
	require_once("lib/idiorm.php");
	require_once('lib/ongair/whatsprot.class.php');

	echo "Start of Jobs: \r\n";
	echo "End of Jobs: \r\n";

	// ORM::configure('sqlite:./demo.sqlite');
	ORM::configure('mysql:host=127.0.0.1:8889;dbname=wassup');
	ORM::configure('username', 'root');
	ORM::configure('password', 'root');

	$accounts = ORM::for_table('accounts')->where(array('setup' => false, 'off_line' => true))->find_many();

	foreach ($accounts as $account) {
		echo "Account : ".$account->name." - ".$account->phone_number." \r\n";
		// echo "Account : ".$account->phone_number." \r\n";

		// find any pending jobs

		$jobs = ORM::for_table('job_logs')->where(array('account_id' => $account->id, 'sent' => false))->find_many();
		$num_jobs = count($jobs);
		echo "Number of jobs ".$num_jobs." \r\n";

		if ($num_jobs > 0) {
			$w = new WhatsProt($account->phone_number, createIdentity($account->phone_number), $account->name, true);
			$w->connect();
			$w->loginWithPassword($account->whatsapp_password);	

			foreach ($jobs as $job) {
				# code...
				echo "Job: ".$job->method."\r\n Args: ".$job->args."\r\n";
				$worked = false;

				if ($job->method == "setStatusMessage") {
					$w->sendStatusUpdate($job->args);										
					$worked = true;
				}
				elseif ($job->method == "sendProfilePicture") {
					// echo "About to send profile picture.\r\n";
					$w->sendSetProfilePicture($job->args);
					$worked = true;
				}

				if ($worked)
				{
					$job->sent = true;
					$job->save();
					sleep(5);	
				}				
			}
		}		
	}

	function createIdentity($username) {
		return strtolower(sha1($username, false));
	}
?>