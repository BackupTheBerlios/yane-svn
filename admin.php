<?php
	include('config.php');
	
	// Preprocessing POST-Data
	if (isset($_POST['all-delete'])) {						// Alle gew�lten sollen gel�cht werden => wurde auf Buzza geklickt
		$mlink=sql_connect();
		foreach ($_POST as $x=>$y) {
			if (strcmp(@substr($x,0,4),'user')==0) {		// Checkbox markiert?
				user_unset($y,array($lists[$_POST['list']]->name));
//				$result=mysql_query('DELETE FROM `'.$mysql_table.'` WHERE `mailaddress` = \''.$y.'\' AND `'.$_POST['list'].'` = \'1\';') or
//						die('There was a problem with the request "'.$sql.'".'."\n".'MySQL error: '.mysql_error());
			}
		}
		mysql_close($mlink);
	}
	if (isset($_POST['delete'])) {
		$mlink=sql_connect();
		user_unset($_POST['delete'],array($lists[$_POST['list']]->name));
		mysql_close($mlink);
	}
	
	switch ($_GET['page']) {
	
		case '': {
			break;
		}
		
///////////////////////////////////////////////////////////////////////////////////// COMPOSE ////////////////////////////////////////////////
		
		case 'compose': {
			echo '<html>
	<head>
		<title>Neue Mail an Subscriber verfassen</title>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<form method="post" enctype="mulipart/form-data" action="send.php">
			<table border="0">
				<tr>
					<td><label for="subject">Betreff:</label></td>
					<td><input type="text" name="Subject" value="" id="subject" style="width: 100%;" /></td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><label for="body">Inhalt:</label></td>
					<td><textarea name="messagebody" value="" id="body" rows="20" cols="75"></textarea></td>
				</tr>
				<tr>
					<td>Listen:</td>
					<td>';
			$count=count($lists);
			for ($x=0;$x<$count;$x++) {
				echo '
						<input type="checkbox" name="'. $lists[$x]->name .'" ';	
//				if ((strlen($_GET['lists'])) && (((int)$_GET['lists']) & ($x+1))) echo 'checked ';
				if (isset($_POST[$lists[$x]->name])) echo 'checked ';
				echo 'id="'. $x .'" /><label for="'. $x .'"> '. $lists[$x]->name .'</label>';
				if ($x-1<$count) echo '<br />';
			}
			echo '
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;"><input type="submit" name="send" value="Mails versenden" /></td>
				</tr>
			</table>
			<input type="hidden" name="jobbegin" value="<?echo time();?>" />
			<input type="hidden" name="ip" value="'.getenv('REMOTE_ADDR').'" />
		</form>
	</body>
</html>';
			break;
		}
		
///////////////////////////////////////////////////////////////////////////////////// SETTINGS ///////////////////////////////////////////////
		
		case ('' || 'settings'): {
			echo '
<html>
	<head>
		<title>Konfiguration von Yane</title>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<h1>Konfiguration von Yane</h1>
		<p>
			Das Konfigurationsmen&uuml; von Yane. Hier k&ouml;nnen Sie sowhol die Mailinglisten als auch deren Benutzer verwalten.
		</p>
		<h2>Mail verfassen</h2>
		<p>
			<form method="post" action="admin.php?page=compose">';
			$count=count($lists);
			for ($x=0;$x<$count;$x++) {
				echo '
				<input type="checkbox" name="'. $lists[$x]->name .'" value="'.$x.'" id="m'. $x .'" /><label for="m'. $x .'"> '. $lists[$x]->name .'</label>';
				if ($x-1<$count) echo '<br />';
			}
			echo '
				<input type="submit" name="compose" value="Mail verfassen" />
			</form>
		</p>
		<h2>Mailinglisten anlegen / bearbeiten / l&ouml;schen</h2>
		<a name="subscribers"><h2>Subscribers einsehen</h2></a>
		<p>
			Bitte w&auml;hlen Sie die Liste aus, deren Benutzer Sie einsehen m&ouml;chten:
			<form method="post" action="admin.php?page=settings#subscribers" name="slsubscribers">';
			$count=count($lists);
			for ($x=0;$x<$count;$x++) {
				echo '
				<input type="radio" name="list" value="'.$x.'"';
				if (isset($_POST['list'])) {
					if ((int)$_POST['list']==$x) echo 'checked ';
				} else {
					if ($x==0) echo 'checked ';
				}
				echo 'id="s'. $x .'" /><label for="s'. $x .'"> '. $lists[$x]->name .'</label>';
				if ($x-1<$count) echo '<br />';
			}
			echo '
				<input type="submit" name="subscribers" value=" OK " />
			</form>
		</p>';
			if (isset($_POST['subscribers']) && isset($_POST['list'])) {
				/* Form:
				 * subscribers => " OK "
				 * list => $_POST['list']
				 * user[EMAIL] => selected
				 * delete => [EMAIL]
				 * all-delete => "Gew&auml;hlte L&ouml;schen"
				 */
				echo '
		<p>
			<form method="post" enctype="multipart/form-data" name="dlsubscribers">
				<input type="hidden" name="subscribers" value=" OK " />
				<input type="hidden" name="list" value="'.$_POST['list'].'" />
				<table border="1">
					<tr class="headline">
						<td></td>
						<td>E-Mail-Adresse</td>
						<td>Name</td>
						<td><span title="Fehlgeschlagene Login-Versuche seit letztem erfolgreichen Login">Anzahl Fehlversuche</span></td>
						<td>Aktionen</td>
					</tr>';
				$sql='SELECT `mailaddress`, `name`, `failedlogins` FROM `'.$mysql_table.'` WHERE `'.$lists[(int)$_POST['list']]->name.'` = \'1\'';
				$mlink=sql_connect();
				if (!$result=mysql_query($sql)) die('There was a problem with the request "'.$sql.'".'."\n".'MySQL error: '.mysql_error());
				mysql_close($mlink);
				while ($entry=mysql_fetch_assoc($result)) {
					echo '
					<tr>';
					$x=0; $y=0;
					foreach ($entry as $col_value) {
						if ($x==0) {
							echo '<td style="border: 0px;"><input type="checkbox" name="user'.$y.'" value="'.$col_value.'" />';
							$email=$col_value;
						}
						echo '
						<td>'.$col_value.'</td>';
						//$data[] = $col_value;
						$x++;
					}
					echo '
						<td style="border: 0px;">
							<button type="submit" name="delete" value="'.$email.'" title="'.$email.' von Mailingliste l&ouml;schen">
								<img src="./remove.png" title="'.$email.' von Mailingliste l&ouml;schen" alt="Benutzer von Mailingliste l&ouml;schen" width="16" height="16" />
							</button>
						</td>
					</tr>';
					$y++;
				}
				echo '
				</table>
				<input type="reset" name="reset" value="Reset" />
				<input type="submit" name="all-delete" value="Gew&auml;hlte L&ouml;schen" />
			</form>
		</p>';
			}
			echo '
	</body>
</html>';
			break;
		}
	}
	
?>