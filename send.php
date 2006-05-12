<?php
	include('config.php');
	
	$debug=((int)$_GET['debug'])?((int)$_GET['debug'])(0);
	$body=$_POST['body'];
	$subject=$_POST['subject'];
	$header='From: '.$absender."\r\n".'Reply-To: '.$absender."\r\n".'X-Mailer: Yane: PHP/'.phpversion();
	
	$listcount=0; $y=0;
	$mlink=sql_connect();
	foreach ($_POST as $key=>$list) {
		if (strcmp(substr($key,0,4),'list')==0) {
			$sqls[$listcount]='SELECT `mailaddress` FROM `'.$mysql_table.'` WHERE `'.$list.'` = \'1\' LIMIT `'.(int)substr($key,5).','.$bulk_send.'\';';
			if ($debug) {
				echo '<b>SQL-Befehl '.$listcount.'</b>: ';
				var_dump($sqls[$listcount];
			}
			$listen[$listcount]=$list;
			$listnext[$listcount]=(int)substr($key,5)+$bulk_send;
			$result=mysql_query($sqls[$listcount]) or die('There was a problem with the request "'.$sqls[$listcount].'".'."\n".'MySQL error: '.mysql_error());
			while ($entry=mysql_fetch_assoc($result)) {
				$addy[$listcount][]=$entry[0];
				$y++;
			}
//			if ($y>=$bulk_send) break;
			if ($y>=$buld_send) $count=$listcount;
			$listcount++;
		}
	}
	mysql_close($mlink);
	
	$b=0;
	for ($z=0;$z<=$count;$z++) {
		$bla=$addy[$z];
		if (is_array($bla)) {
			foreach ($bla as $key=>$a) {
				mail($a,$subject,$body,$header);
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
	
?> 