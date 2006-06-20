<?php
    
    include '../functions.php';
    
    #Is this a proper key? Possibly invalid format of the key, injection-attempt, etc...
    $act_key=$_POST['act_key'];
    $act_key=trim($act_key);
    if (!preg_match('/^[a-z|A-Z|0-9]{32}$/', "$act_key")) {
    header("Location: no_such_key.html");
    die();
    }
    
    #Open MySQL-DB
    $link = open_db();
    
    #Has someone this activation key?
    $result = mysql_query("SELECT * FROM User WHERE activation_key = CONVERT( _utf8 '$act_key' USING latin1 ) COLLATE latin1_swedish_ci;") OR die(mysql_error());
    #If no, show error
    if(mysql_num_rows($result) == 0) {
    mysql_close($link);
    header("Location: no_such_key.html");
    die();
    } else {
    #If yes, set activation-key to NULL
    mysql_query("UPDATE User SET activation_key = NULL WHERE CONVERT( activation_key USING utf8 ) = '$act_key';") OR die(mysql_error());
    mysql_close($link);
    
    #Send mail, but on error don't die, because mail is for convenience only
    $subject = 'Your account has been activated';
    $message = "Thank you for activating your YaCy newsletter account.\nYou now can login with the login-data supplied in the earlier mail.\n\nUp to now, you *have not* been subscribed to any list, please go to http://newsletters.yacy-forum.de/manage/subscriptions now and choose the newsletters you want to receive in the future.\n\nKind regards,\nthe YaCy newsletter team";
    $headers = 'From: do-not-reply@newsletters.yacy-forum.de' . "\r\n";
    #mail($mailadresse, $subject, $message, $headers);
    
    header("Location: success.html");
    }

?>