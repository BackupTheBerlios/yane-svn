<?php

    include '../functions.php';
    
 
    #-------------------------------------------------------------------------------------------------------------------------
    
	#get email-address from POST
    $mailaddress=$_POST['mailaddress'];
    
    $mailaddress=trim($mailaddress);
    
    #Check email-address for @ and spam-attacks
    if (!validate_email($mailaddress)) {
    header("Location: email_wrong.html");
    die();
    }
	
	#-------------------------------------------------------------------------------------------------------------------------
	
	#Open MySQL-DB
    $link = open_db();

    #Check if the user already has registered
    $result = mysql_query("SELECT * FROM Yane WHERE email_address = '$mailaddress' AND activation_key IS NULL;") OR die(mysql_error());
    # User exists and is activated
	if(mysql_num_rows($result) == 1) {
    header("Location: user_exists.html");
    mysql_close($link);
    die();
    } else {
        # User exists, but is not activated --> Send new key
        $result = mysql_query("SELECT * FROM Yane WHERE email_address = '$mailaddress' AND activation_key IS NOT NULL;") OR die(mysql_error());
        if(mysql_num_rows($result) == 1) {
        $token = generate_activation_key($mailaddress);
        #Resend confirmation mail to user
        send_mail($mailaddress, "act_resend", "http://newsletters.yacy-forum.de/activate", $mailaddress, $ip, $token);
        #Show success
        header("Location: user_exists_act_resend.html");
        die();
    }
    }
	
	#-------------------------------------------------------------------------------------------------------------------------
	
    # Check if the passwords match
    $plain_pw1=$_POST['password1'];
    $plain_pw2=$_POST['password2'];
    
    if (!($plain_pw1==$plain_pw2)) {
    header("Location: pws_dont_match.html");
    die();
    }
    
	#-------------------------------------------------------------------------------------------------------------------------
	
    #If user doesn't exist:
    
    #Add new user
    #-------------------------------------------------------------------------------------------------------------------------    
    add_user($mailaddress);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Set password
    #-------------------------------------------------------------------------------------------------------------------------
    update_PW($mailaddress, $plain_pw1);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Log time of last modification  on dataset
    #-------------------------------------------------------------------------------------------------------------------------
    log_change($mailaddress);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Initialize the login values
    #-------------------------------------------------------------------------------------------------------------------------
	#Get the user's IP
    $ip=$_SERVER['REMOTE_ADDR'];
    log_correct_login($mailaddress, $ip);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Generate and save activation key
    #-------------------------------------------------------------------------------------------------------------------------
    $token = generate_activation_key($mailaddress);
    #-------------------------------------------------------------------------------------------------------------------------

    #If we didn't die() up to now, everything was successful
    
    #Send confirmation mail to user
    #send_mail($mailadresse, "register", "http://newsletters.yacy-forum.de/activate", $mailaddress, $ip, $token);
    
	#-------------------------------------------------------------------------------------------------------------------------
	
    #Show success
    header("Location: success.html");
    
?>
