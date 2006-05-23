<?php
	include('config.php');
	echo str_pad('',1024)."\n";  // minimum start for Safari
	$debug=((int)$_GET['debug'])?((int)$_GET['debug']):(0);
	$body=$_POST['body'];
	$subject=$_POST['subject'];
	$header='From: '.$absender."\r\n".
			'Reply-To: '.$absender."\r\n".
			'X-Mailer: Yane: PHP/'.phpversion();
	
	echo '<html>
	<head>
		<title>Sending mails...</title>
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	<body>';
	
	if ($debug) {
		echo '
		<pre>';
		echo 'POST-Data:'."\n";
		var_dump($_POST);
		echo '</pre>';
		flush();
	}
	
	$listcount=0; $y=0;
	if ($debug) {
		echo '
		Connecting to MySQL-Server...<br />';
		flush();
	}
	$mlink=sql_connect();
	if ($debug && isset($mlink)) {
		echo '
		Fetching addresses from database...<br />';
		flush();
	}
	foreach ($_POST as $key=>$list) {
		if (strcmp(substr($key,0,4),'list')==0) {
			if ($list=='on') $list=0; 		// so übergeben von checkbox aus admin.php?page=compose => start der schleife
			$sqls[$listcount]='SELECT `mailaddress` FROM `'.$mysql_table.'` WHERE `'.substr($key,4).'` = \'1\' LIMIT '.(int)$list.', '.$bulk_send;
			if ($debug) {
				echo '
		&nbsp;&nbsp;SQL-Befehl '.$listcount.': '.$sqls[$listcount].'<br />';
				flush();
			}
			$listen[$listcount]=substr($key,4);
			$listnext[$listcount]=(int)substr($key,4)+$bulk_send;
			$result=mysql_query($sqls[$listcount]) or die('There was a problem with the request "'.$sqls[$listcount].'".'."\n".'MySQL error: '.mysql_error());
			while ($entry=mysql_fetch_assoc($result)) {
				$addy[$listen[$listcount]][]=$entry['mailaddress'];
				$y++;
			}
			if ($debug) {
				echo '
		Found '.$y.' Entries in Database "'.$listen[$listcount].'".<br />';
				flush();
			}
//			if ($y>=$bulk_send) break;
			if ($y>=$buld_send) $count=$listcount;
			$listcount++;
		}
	}
	if ($debug) {
		echo '
		Closing Connection to MySQL-Server...<br />';
		flush();
	}
	mysql_close($mlink);
	
	if ($debug) {
		echo '
		<pre>';
		echo 'Found the following addresses:'."\n";
		var_dump($addy);
		echo '
		</pre>';
		flush();
	}
	
	$b=0;
	for ($z=0;$z<=$count;$z++) {
		$bla=$addy[$z];
		$list=key($addy);
		if ($debug) {
			echo '
		Now processing '.count($bla).' members of list '.$list.'.<br />';
			flush();
		}
		if (is_array($bla)) {
			foreach ($bla as $key=>$a) {
				if ($debug) {
					echo '
		Sending mail to '.$a.' in list '.$list.'<br />';
					flush();
				}
				//$result=mail($a,$subject,$body,$header);
				if (!$result) echo 'mail to '.$a.' in list '.$list.' could not be sent.'."\n";
				unset($addy[$z][$key]);
				$b++;
				if ($b==$bulk_send) break;
			}
			if (count($addy[$z])==0) {
				unset($addy[$z]);
				unset($listnext[$z]);
				unset($listen[$z]);
			}
			if ($b==$bulk_send) break;
		}
	}
	/*
	// erzeuge einen neuen cURL-Handle
	$ch = curl_init();

	// setze die URL und andere Optionen
	curl_setopt($ch,CURLOPT_URL,'send.php');
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_POSTFIELDS,'body='.urlencode($body));
	curl_setopt($ch,CURLOPT_POSTFIELDS,'subject='.urlencode($subject));
	for ($z=0;$z<=$x;$z++) {
		if (isset($listen[$z])) {
			curl_setopt($ch,CURLOPT_POSTFIELDS,'list'.$listnext[$z].'='.urlencode($listen[$z]));
		}
	}

	// führe die Aktion aus und gebe die Daten an den Browser weiter
	curl_exec($ch);

	// schließe den cURL-Handle und gebe die Systemresourcen frei
	curl_close($ch);
	*/
?> 