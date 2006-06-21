<?php
    
    include '../functions.php';
    
    #Is this a proper key? Possibly invalid format of the key, injection-attempt, etc...
    $act_key=$_GET['act_key'];
    $act_key=trim($act_key);
    if (!preg_match('/^[a-z|A-Z|0-9]{32}$/', "$act_key")) {
    header("Location: no_such_key.html");
    die();
    }
    
    #Open MySQL-DB
    $link = open_db();
    
    #Has someone this activation key?
    $result = mysql_query("SELECT * FROM User WHERE activation_key = CONVERT( _utf8 '$act_key' USING latin1 ) COLLATE latin1_swedish_ci") OR die(mysql_error());
    #If no, show error
    if(mysql_num_rows($result) == 0) {
    mysql_close($link);
    header("Location: no_such_key.html");
    die();
    } else {
    #Get mailaddress
    $result = mysql_query("SELECT email_address FROM User WHERE activation_key = '$act_key';") OR die(mysql_error());
    $row = mysql_fetch_assoc($result);
    
    #If yes, set activation-key to NULL
    mysql_query("UPDATE User SET activation_key = NULL WHERE CONVERT( activation_key USING utf8 ) = '$act_key'") OR die(mysql_error());
    mysql_close($link);
    
    #Send mail
    send_mail($row['email_address'], "activated", "http://newsletters.yacy-forum.de/activate");
    
    header("Location: success.html");
    }

?>