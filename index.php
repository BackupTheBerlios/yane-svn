<?php

	include('config.php');
	
	$title="An-/Abmeldung bei der $project-Mailinglist";
	
	echo '<html>
	<head>
		<title>'. $title .'</title>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<h1>'. $title .'</h1>';
	
	if (isset($_POST['submit'])) {
		// Prüfen, ob alle Angaben korrekt sind
		if ((strpos($_POST['address'],'@')===false) or (strpos($_POST['address'],'.')===false)) die('<i>Bitte geben Sie Ihre korrekte E-Mail-Adresse an.</i>');
		if (strcmp($_POST['pwd1'],$_POST['pwd2'])!=0) die('<i>Die Passw&ouml;rter stimmen nicht &uuml;berein.</i>');
		if (strlen($_POST['pwd1'])<6) die('<i>Das Passwort muss l&auml;nger als 6 Zeichen sein.</i>');
		if (strlen($_POST['pwd1'])>200) die('<i>Das Passwort muss k&uuml;rzer als 200 Zeichen sein um den Server nicht unn&ouml;tig (kann sich doch niemand merken) zu belasten.</i>');
		$selected=array();
		foreach ($lists as $list) {
			if (isset($_POST[$list->name])) array_push($selected,$list);
		}
		if (count($selected)==0) die('<i>Bitte w&auml;hlen Sie mindestens eine Liste aus.</i>');
		
/* 		// Prüfen, ob die Mail-Adresse auch in allen Listen vorhanden ist => $selected1
		$error=false;
		$selected1=array();
		foreach ($selected as $list) {
			if (!array_search($_POST['address'],$list->users)) {
				if (!$error) echo ($_POST['action']=='subscribe')?('<i>In folgende Listen konnten Sie nicht eingetragen werden:</i><ul>'):('<i>Aus folgenden Listen konnten Sie nicht ausgetragen werden:</i><ul>');
				echo '
			<li>'. $list->name .'</li>';
				$error=true;
			} else {
				array_push($selected1,$list);
			}
		}
		if ($error)	echo '
			</ul>'; */
		
		// Prüfen ob Benutzer-Adresse in DB vorhanden ist
		$mlink=sql_connect();
		$sql='SELECT * FROM `'. $mysql_table .'` WHERE `mailaddress` = \''. $_POST['address'] .'\'';
		$result=mysql_query($sql,$mlink);
		if ($result===false) die('There was a problem with the request "'.$sql.'".'."\n".'MySQL error: '.mysql_error());
		
		switch ($_POST['action']) {
			case 'subscribe': {
				if (mysql_fetch_array($result)) die('<i>Die angegebene E-Mail-Adresse ist bereits registriert.</i>');
				insert_user($_POST['address'],(strlen($_POST['name'])>0)?($_POST['name']):(''),$pwd1,$selected);
				break;
			}
			case 'unsubscribe': {
				if (!mysql_fetch_array($result)) die('<i>Die angegebene E-Mail-Adresse ist nicht registriert.</i>');
				user_unset($_POST['address'],$selected);
				break;
			}
		}
		mysql_close($mlink);
	} else {
		echo '
		<form method="post" enctype="multipart/form-data" action="#">
			<table border="0">
				<tr><td>Name:</td><td><input type="text" name="name" value="" /></td></tr>
				<tr><td>E-Mail-Adresse:</td><td><input type="text" name="address" value="" /></td></tr>
				<tr><td>Passwort:</td><td><input type="password" name="pwd1" value="" /></td></tr>
				<tr><td>Passwort (Wiederholung):</td><td><input type="password" name="pwd2" value="" /></td></tr>
				<tr>
					<td>Listen:</td>
					<td>';
		$count=count($lists);
		for ($x=0;$x<$count;$x++) {
			echo '
							<input type="checkbox" name="'. $lists[$x]->name .'" value="true"';
			if ($lists[$x]->default_checked===true) echo ' checked';
			echo ' id="'. $x .'" /><label for="'. $x .'"> '. $lists[$x]->name .'</label>';
			if ($x-1<$count) { echo '<br />'; }
		}
		echo '
					</td>
				</tr>
				<tr>
					<td>Aktion:</td>
					<td>
						<input type="radio" name="action" value="subscribe" checked id="true" /><label for="true"> Subscribe</label><br />
						<input type="radio" name="action" value="unsubscribe" id="false" /><label for="false"> Unsubscribe</label>
					</td>
				</tr>
				<tr><td colspan="2"><center><input type="submit" value="Absenden" name="submit" /></center></td></tr>
			</table>
		</form>';
	}
	
	echo '
	</body>
</html>';
?>