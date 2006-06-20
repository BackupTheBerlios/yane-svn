<?php
    include '../functions.php';    
    
    #To create a new user we have to do the following:
    #Check, if this address is already registered
    #If yes, look if activation suceeded.
    #If yes, die.
    #If no, resend the activation code
    
    #------------------------------
    #get email-address from POST
    $mailadresse=$_POST['mailaddress'];
    
    $mailadresse=trim($mailadresse);
    
    #Check email-address for @ and spam-attacks
    if (!preg_match("/^[\w.+-]{1,64}\@[\w.-]{1,255}\.[a-z]{2,6}$/",$mailadresse)){ 
    header("Location: email_wrong.html");
    die();
    }
    #------------------------------
    
    #Open MySQL-DB
    $link = open_db();

    #Check if the user already has registered
    $result = mysql_query("SELECT * FROM User WHERE email_address = '$mailadresse' AND activation_key IS NULL;") OR die(mysql_error());
    if(mysql_num_rows($result) == 1) {
    header("Location: user_exists.html");
    mysql_close($link);
    die();
    } else {
    #TO-DO: If registered, but not activated send new key
    }

    #If user doesn't exist:
    #Create new user in database:
    # email_address   varchar(255)  	
    # md5_password   varchar(32)
    # security_list   	tinyint(1) 
	# announce_list  	tinyint(1)	  	
	# newsletter_list  tinyint(1) 
	# last_change  datetime
	# last_login  datetime 
	# last_login_ip  varchar(16) 
	# login_failures  int(11) 
	# activation_key  varchar(12)  
    
    #email-address
    #---------------------------------------------------------------------    
    $sql_mailadresse="INSERT INTO User (email_address ) VALUE ('$mailadresse')";
    #---------------------------------------------------------------------
    
    #password
    #---------------------------------------------------------------------
    #get password from POST
    $password=$_POST['password'];
    
    $pw_md5 = md5($password);
    
    $sql_pw="UPDATE User SET md5_password = '$pw_md5' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    #---------------------------------------------------------------------
    
    #Disable all lists
    #---------------------------------------------------------------------
    $sql_lists="UPDATE User SET security_list = '0', announce_list = '0', newsletter_list = '0' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    #---------------------------------------------------------------------
    
    #last change time
    #---------------------------------------------------------------------
    #log_change();
    #---------------------------------------------------------------------

    #last_login
    #---------------------------------------------------------------------
    $sql_last_login="UPDATE User SET last_login = '$time' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    #---------------------------------------------------------------------
    
    #last_login_ip
    #---------------------------------------------------------------------
    $ip=$_SERVER['REMOTE_ADDR'];
    $sql_last_login_ip="UPDATE User SET last_login_ip = '$ip' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    #---------------------------------------------------------------------
    
    #login_failures
    #---------------------------------------------------------------------
    $sql_login_failures="UPDATE User SET login_failures = '0' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    #---------------------------------------------------------------------
    
    #activation_key
    #---------------------------------------------------------------------
    $token = md5(uniqid(rand(), true));
    $sql_activation_key="UPDATE User SET activation_key = '$token' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse'";
    #---------------------------------------------------------------------

    #Write everything
    mysql_query($sql_mailadresse) or die('Address save failed: ' . mysql_error());
    mysql_query($sql_pw) or die('Password save failed: ' . mysql_error());
    mysql_query($sql_lists) or die('List save failed: ' . mysql_error());
    log_change($mailadresse, $pw_md5) or die('Last-modification-time save failed: ' . mysql_error());
    mysql_query($sql_last_login) or die('Last_login save failed: ' . mysql_error());
    mysql_query($sql_last_login_ip) or die('IP save failed: ' . mysql_error());
    mysql_query($sql_login_failures) or die('Login_failures save failed: ' . mysql_error());
    mysql_query($sql_activation_key) or die('Activation_key save failed: ' . mysql_error());
    mysql_close($link);
    
    #If we didn't die() up to now, everything was successful
    
    #Send confirmation mail to user
    $subject = 'Please activate your account';
    $message = "You registered this email-address on the YaCy newsletter system.\n\nYou must activate your account, before you can use it.\nTo do so, visit http://newsletters.yacy-forum.de/activate and enter the confirmation code there.\n\nRegistered email-address: $mailadresse\nRegistrant's IP: $ip\nConfirmation website: http://newsletters.yacy-forum.de/activate\nConfirmation code: $token\n\nIf someone else registed your address on our system, delete this email and you won't get any mail from us.\n\nRegards,\nthe YaCy newsletter team";
    $headers = 'From: do-not-reply@newsletters.yacy-forum.de' . "\r\n";
    
    if (!mail($mailadresse, $subject, $message, $headers))
    #Error-/Informationpage if mail couldn't be handed to MTA of the server
    die ("We had an unexpected error during queuing mails. Please note the following information and enter it manually at http://newsletters.yacy-forum.de/activation : 
    Registered email-address: $mailadresse
    Registrant's IP: $ip
    Confirmation website: http://newsletters.yacy-forum.de/activation
    Confirmation code: $token");
    
    #Show success
    header("Location: success.html");
    
?>