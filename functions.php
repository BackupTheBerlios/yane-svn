<?php

    include 'config/listdata.php';
    include 'config/access_data.php';
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    function open_db()
    {
    $handler = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR die("Couldn't establish connection to database. Error: " . mysql_error());
    mysql_select_db(MYSQL_DATABASE) OR die("Couldn't use database. Error: " . mysql_error());
    return $handler;
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    function log_change($mailadresse)
    {
    $link = open_db();
    $time=date('Y-m-d H:i:s');
    $sql="UPDATE User SET last_change = '$time' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    mysql_query($sql) OR die("Couldn't log change. Error: " . mysql_error());
    mysql_close($link);
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    function show_version()
    {
    echo("Powered by <a href='http://developer.berlios.de/projects/yane/'>Yane</a> Ver. 0.5");
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Generates a new activation key, writes it to the DB and returns the key
    function generate_activation_key($mailaddress)
    {
    $link = open_db();
    $token = md5(uniqid(rand(), true));
    $sql="UPDATE User SET activation_key = '$token' WHERE CONVERT( email_address USING utf8 ) = '$mailaddress'";
    mysql_query($sql) OR die('Activation key save failed: ' . mysql_error());
    mysql_close($link);
    return $token;
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Sends a mail-template to the given address. $topic must be name of template.
    function send_mail($mailaddress, $topic, $a="", $b="", $c="", $d="", $e="", $f="")
    {
    $fp = fopen(ROOTDIR . "config/emails/$topic" . ".txt",'r');
    if ($fp)
    {
    $subject = fgets($fp);
    $message="";
       while (!feof($fp))
       {
          $line = fgets($fp);
          $message = $message .= $line;
       }
       fclose($fp);
    }
    else
       die("Unkown email-template: $topic");
       
    $sender = EMAIL_SENDER;
    
    if (!$a=="") {
    $message = str_replace("%1%", $a, $message);
    }
    if (!$b=="") {
    $message = str_replace("%2%", $b, $message);
    }
    if (!$c=="") {
    $message = str_replace("%3%", $c, $message);
    }
    if (!$d=="") {
    $message = str_replace("%4%", $d, $message);
    }
    if (!$e=="") {
    $message = str_replace("%5%", $d, $message);
    }
    if (!$f=="") {
    $message = str_replace("%6%", $d, $message);
    }
    #Send the mail or, in case of error, print it.
    if (!mail($mailaddress, $subject, $message, "From: $sender\r\n"))
    die("Error queuing mail. Here the content: $message");
    
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Checkes for a valid email-address and spam-attempts
    function validate_email($mailaddress)
    {
    
    if (preg_match("/^[\w.+-]{1,64}\@[\w.-]{1,255}\.[a-z]{2,6}$/",$mailaddress)){ 
    return true;
    } else {
    return false;
    }
    
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Adds a new user to the database
    function add_user($mailaddress){
    $link=open_db();
    $sql="INSERT INTO User (email_address ) VALUE ('$mailaddress')";
    mysql_query($sql) OR die("Couldn't add new user to database. Error: " . mysql_error());
    mysql_close($link);
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Updates the password entry of an user
    function update_pw($mailaddress, $plain_pw){
    $pw_md5 = md5($plain_pw);
    $link=open_db();
    $sql="UPDATE User SET md5_password = '$pw_md5' WHERE CONVERT( email_address USING utf8 ) = '$mailaddress'";
    mysql_query($sql) OR die("Couldn't update password. Error: " . mysql_error());
    mysql_close($link);
    }
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Logs the IP of the user and sets login_failures to 0
    function log_correct_login($mailaddress, $ip){
    $link=open_db();
    $sql="UPDATE User SET last_login_ip = '$ip' WHERE CONVERT( email_address USING utf8 ) = '$mailaddress'";
    mysql_query($sql) OR die("Couldn't save last login IP. Error: " . mysql_error());
    $sql="UPDATE User SET login_failures = '0' WHERE CONVERT( email_address USING utf8 ) = '$mailaddress'";
    mysql_query($sql) OR die("Couldn't reset login failures. Error: " . mysql_error());
    $time=date('Y-m-d H:i:s');
    $sql="UPDATE User SET last_login = '$time' WHERE CONVERT( email_address USING utf8 ) = '$mailaddress'";
    mysql_query($sql) OR die("Couldn't update login timestamp. Error: " . mysql_error());
    mysql_close($link);
    }
    
    #-------------------------------------------------------------------------------------------------------------------------

?>