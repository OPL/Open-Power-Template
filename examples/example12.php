<?php 
	define('OPT_DIR', '../lib/');
	require('../lib/opt.class.php');
	require('../lib/opt.components.php');
	try{ 
		$tpl = new optClass; 
		$tpl -> root = './templates/';
		$tpl -> compile = './templates_c/';
		$tpl -> gzipCompression = 1;

		require('db_connect.php'); 

		$tpl -> httpHeaders(OPT_HTML);

		if(isset($_POST['yes']))
		{
			die('Thank you for your submission, '.$_POST['username'].'!');
		}
		else
		{
			if(isset($_POST['ok']))
			{
				$username = new textLabelComponent;
				$username -> set('name', 'username');
				$username -> set('value', $_POST['username']);
				$username -> set('message', 'Is this correct?');	

				$actions = array(0 =>
					array(
						'name' => 'yes',
						'value' => 'Yes',
						'type' => 'submit'
					),
					array(
						'name' => 'no',
						'value' => 'No',
						'type' => 'submit'
					)
				);
			}
			else
			{
				$username = new textInputComponent;
				$username -> set('name', 'username');
				if(isset($_POST['username']))
				{
					$username -> set('value', $_POST['username']);			
				}
				$actions = array(0 =>
					array(
						'name' => 'ok',
						'value' => 'OK',
						'type' => 'submit'
					)	
				);
			}
		}

		$tpl -> assign('name', $username);
		$tpl -> assign('actions', $actions);

		$tpl -> parse('example12.tpl');
		mysql_close();
	}
	catch(optException $exception)
	{ 
		optErrorHandler($exception); 
	}
?>
