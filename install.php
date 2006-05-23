<?php
	
	function create_table($tablename) {
		$sql=	'CREATE TABLE `'. $mysql_table .'` ('
				.' `mailaddress` TEXT NOT NULL, '
				.' `name` TEXT NOT NULL, '
				.' `password` TEXT NOT NULL, '
				.' `lastlogin` INT NOT NULL, '
				.' `lastfailedlogin` DATETIME NOT NULL, '
				.' `failedlogins` INT NOT NULL'
				.' )'
				.' TYPE = myisam'
				.' COMMENT=\'The Yane UserDB\';';
		$result=mysql_query($sql);
		return $result;
	}
	
	switch(isset($_POST['submit'])) {
		
		case true: {
			echo str_pad('',1024).'
<html>
	<head><title>Installaton of Yane in progress...</title></head>
	<body>
		<p>';
			
			// Connect to MySQL-Server
			if (!$link=mysql_connect($_POST['mysql-server'] .':'. $_POST['mysql-port'],$_POST['mysql-user'],$_POST['mysql-pass'],true)) {
				die('ERROR: Failed to connect to MySQL-Server. No connection could be established, please verify your settings.');
			} else {
				echo 'Successfully connected to MySQL-Server.<br />';
				flush();
			}
			
			// Create the table
			if (!create_table($_POST['mysql-table-prefix'] .'yane')) {
				die('Could not create specified table.'."\n".'MySQL error: '.mysql_error());
			} else {
				echo 'Successfully created table "'. $_POST['mysql-table-prefix'] .'yane" for Yane.<br />';
				flush();
			}
			mysql_close($link);
			
			// Open an SMTP connection
			$ip=gethostbyname($_POST['smtp-server']);
			echo 'Resolved "'. $_POST['smtp-server'] .'" to '. $ip .'.<br />';
			$sock=fsockopen($ip.':'.$_POST['smtp-port]']);//,&$errno,&$errstr,30);
			if ($sock===false) {
				die('Could not establish a connection to specified SMTP-Server "'. $_POST['smtp-server'] . '" on port '. $_POST['smtp-port'] .': '.
					$errstr .'('. $errno .').');
			} else {
				echo 'Established connection to SMTP-Server, waiting for response... ';
				flush();
			}
			$result=fgets($sock,256);
			if(substr($sock,0,3)!='220') {
				die('failed');
			} else {
				echo 'OK<br />';
				flush();
			}
			fclose($sock);
			
		}
		
		case false: {
			echo '<html>
	<head><title>Install Yane</title></head>
	<body>
		<h1>Install Yane</h1>
		<form method="post" action="install.php" enctype="multipart/form-data">
			<h2>MySQL-Server Access</h2>
			<p>
				<table border="1" cellspacing="0" cellpadding="2">
					<tr>
						<td><label for="mysql-server">MySQL-Server Host:</label></td>
						<td><input type="text" name="mysql-server" value="';
			if (get_cfg_var('mysql.default_host')!='') echo get_cfg_var('mysql.default_host'); else echo 'localhost'; 
			echo '" id="mysql-server" />
					</tr>
					<tr>
						<td><label for="mysql-port">MySQL-Server Port:</label></td>
						<td><input type="text" name="mysql-port" value="';
			if (get_cfg_var('mysql.default_port')!='') echo get_cfg_var('mysql.default_port'); else echo '3306';
			echo '" />
					</tr>
					<tr>
						<td><label for="mysql-user">MySQL-Server User:</label></td>
						<td><input type="text" name="mysql-user" value="';
			echo get_cfg_var('mysql.default_user');
			echo '" id="mysql-user" />
					</tr>
					<tr>
						<td><label for="mysql-pass">MySQL-Server Password:</label></td>
						<td><input type="password" name="mysql-pass" value="" id="mysql-pass" />
					</tr>
					<tr>
						<td><label for="mysql-table-prefix">MySQL-Table Prefix:</label></td>
						<td><input type="text" name="mysql-table-prefix" value="yane_" />
					</tr>
				</table>
			</p>
			<h2>SMTP-Server Access</h2>
			<p>
				<table border="1" cellspacing="0" cellpadding="2">
					<tr>
						<td><label for="smtp-server">SMTP-Server Host:</label></td>
						<td><input type="text" name="smtp-server" value="';
			if (get_cfg_var('SMTP')!='') echo get_cfg_var('SMTP'); else echo 'localhost';
			echo '" id="smtp-server" />
					</tr>
					<tr>
						<td><label for="smtp-port">SMTP-Server Port:</label></td>
						<td><input type="text" name="smtp-port" value="';
			if (get_cfg_var('smtp_port')!='') echo get_cfg_var('smtp_port'); else echo '25';
			echo '" id="smtp-server" />
					</tr>
				</table>
			</p>
			<p>
				Yane will generate one table containing the configured mailing-lists and the registered users.
			</p>
			<input type="submit" name="submit" value="Submit" />
		</form>
	</body>
</html>';
			break;
		}
	}
	
?>