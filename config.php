
<?php

	$project='Yane';
	
	$mysql_user='';
	$mysql_password='';
	$mysql_server='localhost';
	$mysql_database='test';
	$mysql_table='yane';
	
	$absender='';
	
	$bulk_send=10;
	
	$lists=array();
	
	class subscriber {
		var $mailaddress; 		// string containing the e-mail address of this subscriber
		var $password;			// string containing the MD5-hash of the subscriber's password
		var $lists;				// array containing the mailing-lists this subscriber subscribed to as 'mlist->$name'
	}
	
	class mlist {
		var $name;				// string containing the unique name of this mailinglist
		var $description;		// string containing the user-defined description of this mailing-list
		var $users;				// array containing all users as 'subscriber' to this mailing-list
		var $default_checked;
		
		function mlist() {
			$this->default_checked=false;
		}
	}
	
	// initial lists-fetch
	$mlink=sql_connect();
	$result = mysql_query('SHOW COLUMNS FROM `'.$mysql_table.'`');
	if (!$result) die('Could not run query: ' . mysql_error());

	if (mysql_num_rows($result)>0) {
		while ($row=mysql_fetch_assoc($result)) {
			$rownr++;
			if ($rownr>6) {
				$newlist=new mlist;
				$newlist->name=$row['Field'];
				$newlist->default_checked=true;
				$newlist->users=array();
				$newlist->description='';
				array_push($lists,$newlist);
			}
		}
	}
	mysql_close($mlink);
	
	function insert_user($address,$name,$pwd,$selected) {
		global $lists;
		$sql=	'INSERT INTO `yane` (`mailaddress`, `name`, `password`, `lastlogin`, `failedlogins`, `lastfailedlogin`';
		foreach ($lists as $list) {
			$sql.=', `'.$list->name.'`';
		}
		$sql.=') VALUES (\''.$address.'\', \''.$name.'\', \''.md5($pwd).'\', \'\', \'\', \'\'';
		foreach ($lists as $list) {
			$sql.=', \'';
			$found=false;
			foreach ($selected as $bla) {
				if (strcmp($list->name,$bla)==0) {
					$found=true;
					break;
				}
			}
			$sql.=($found)?('1'):('0');
			$sql.='\'';
		}
		$sql.=');';
		mysql_query($sql) or die('Could not create new user.'."\n".'MySQL error: '.mysql_error()."\n".$sql);
	}
	
	/*ALTER TABLE `yane` ADD `bla` BOOL NOT NULL ;*/
	
	/* Entfernt einen Benutzer von der/den angegebenen Listen
	 * @Param:
	 * $address:	(String) 		- E-Mail-Adresse des zu bearbeitenden Users
	 * $selected:	array((String))	- Namen der Listen, aus denen der Benutzer ausgetragen werden soll
	 */
	function user_unset($address,$selected) {
		global $mysql_table;
		echo '<br /><br />';
		var_dump($selected);
		echo '<br /><br />';
		$sql='UPDATE `'.$mysql_table.'` SET ';
		for ($x=0;$x<count($selected);$x++) {
			$sql.='`'.$selected[$x].'` = \'0\' ';
			if (($x+1)<count($selected)) $sql.=', ';
		}
		$sql.='WHERE `mailaddress` = \''.$address.'\';';
		echo $sql.'<br />';
		mysql_query($sql) or die('Could not set configuration.'."\n".'MySQL error: '.mysql_error()."\n".$sql);
		$sql='SELECT * FROM `'.$mysql_table.'` WHERE `mailaddress` = \''.$address.'\';';
		$result=mysql_query($sql) or die('Could not set configuration.'."\n".'MySQL error: '.mysql_error()."\n".$sql);
		$row=mysql_fetch_assoc($result);
		$subscriber=false;
		foreach ($row as $subscribed) {
			if ($subscribed) {
				$subscriber=true;
				break;
			}
		}
		if (!$subscriber) remove_user($address);
	}

	function remove_user($address) {
		global $mysql_table,$lists;
		$sql='DELETE FROM `'.$mysql_table.'` WHERE `mailaddress`=\''.$address.'\'';
		foreach ($lists as $list) {
			$sql.='AND `'.$list->name.'` = \'0\'';
		}
		$result=mysql_query($sql);
		if (!$result) die('Could not remove user '.$address.'.'."\n".'MySQL error: '.mysql_error());
		return $result;
	}
	
	function sql_connect() {
		global $mysql_server,$mysql_user,$mysql_password,$mysql_database;
		
		$mlink=mysql_connect($mysql_server,$mysql_user,$mysql_password);
		if (!$mlink) die('Could not connect to MySQL-server.');
		
		$success=mysql_select_db($mysql_database);
		if (!$success) {
			$sql = 'CREATE DATABASE `'.$mysql_database.'`';
			if (mysql_query($sql, $mlink)) {
			   echo "Database my_db created successfully\n";
			} else {
			   echo 'Error creating database: ' . mysql_error() . "\n";
			}
			
			if (!$success) die('Could not switch to specifed database.'."\n".'MySQL error: '.mysql_error());
			$success=mysql_select_db($mysql_database);
			
			if (!$success) die('Could not switch to specifed database.'."\n".'MySQL error: '.mysql_error());
		}
		
		return $mlink;
	}

?>