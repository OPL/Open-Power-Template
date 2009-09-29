<?php

	mysql_connect('localhost:3305', 'root', 'd4pa') or die('The MySQL database is not configured and I can\'t run this example. 
		Configure the connection in the db_connect.php file, and create a database using samples.sql file.');
	mysql_select_db('test') or die('Unknown database.');
?>
