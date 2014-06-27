<?php
	require_once('lib/limonade.php');
	require_once('lib/ongair/whatsprot.class.php');

	date_default_timezone_set('Africa/Nairobi');

	# 1. Setting global options of our application
	function configure()
	{
		# A. Setting environment
		// $localhost = preg_match('/^localhost(\:\d+)?/', $_SERVER['HTTP_HOST']);
		// $env =  $localhost ? ENV_DEVELOPMENT : ENV_PRODUCTION;

		// option('env', $env);
  

		// $dsn = $env == ENV_PRODUCTION ? 'mysql:host=127.0.0.1;port=3306;dbname=<database>' : 'mysql:host=127.0.0.1;port=8889;dbname=wassup';
		// $username = $env == ENV_PRODUCTION ? '<username>' : 'root';
		// $pass = $env == ENV_PRODUCTION ? '<password>' : 'root';
		
		// try
		// {
	 //  		$db = new PDO($dsn, $username, $pass, array( PDO::ATTR_PERSISTENT => false));
		// }
		// catch(PDOException $e)
		// {
	 //  		halt("Connexion failed: ".$e); # raises an error / renders the error page and exit.
		// }

		// $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		// option('db_conn', $db);
	}

	dispatch_post('/', 'home');
	function home() {
		$method = $_POST['method'];
		$jid = $_POST['jid'];

		if ($method == "identity") {			
			$identity = strtolower(sha1($jid, false));
			// $identity = createIdentity($jid);
			
			return json(array( "identity" => $identity ));
		}
		elseif ($method == "register") {
		
			$identity = createIdentity($jid);
			$nickname = $_POST['nickname'];
			$code = $_POST['code'];
			$test = $_POST['test'];

			
			$password = "";
			

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

		return html('home/index.html.php'); # rendering HTML view
	}

	function createIdentity($username) {
		return strtolower(sha1($username, false));
	}

	

	run();
?>