<?php

    include '../functions.php';
    
    #To create a new user we have to do the following:
    #Check, if this address is already registered
    #If yes, look if activation suceeded.
    #If yes, die.
    #If no, resend the activation code
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Initialize some common variables
    
    #get email-address from POST
    $mailadresse=$_POST['mailaddress'];
    
    $mailadresse=trim($mailadresse);
    
    #Check email-address for @ and spam-attacks
    if (!validate_email($mailadresse)){ 
    header("Location: email_wrong.html");
    die();
    }
    
    #Get the user's IP
    $ip=$_SERVER['REMOTE_ADDR'];
    
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Open MySQL-DB
    $link = open_db();

    #Check if the user already has registered
    $result = mysql_query("SELECT * FROM User WHERE email_address = '$mailadresse' AND activation_key IS NULL;") OR die(mysql_error());
    if(mysql_num_rows($result) == 1) {
    header("Location: user_exists.html");
    mysql_close($link);
    die();
    } else {
        #If registered, but not activated --> Send new key
        $result = mysql_query("SELECT * FROM User WHERE email_address = '$mailadresse' AND activation_key IS NOT NULL;") OR die(mysql_error());
        if(mysql_num_rows($result) == 1) {
        $token = generate_activation_key($mailadresse);
        #Resend confirmation mail to user
        send_mail($mailadresse, "act_resend", "http://newsletters.yacy-forum.de/activate", $mailadresse, $ip, $token);
        #Show success
        header("Location: user_exists_act_resend.html");
        die();
    }
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
    
    #Add new user
    #-------------------------------------------------------------------------------------------------------------------------    
    add_user($mailadresse);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Set password
    #-------------------------------------------------------------------------------------------------------------------------
    $plain_pw=$_POST['password'];
    update_PW($mailadresse, $plain_pw);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Log time of last modification  on dataset
    #-------------------------------------------------------------------------------------------------------------------------
    log_change($mailadresse);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Initialize the login values
    #-------------------------------------------------------------------------------------------------------------------------
    log_correct_login($mailadresse, $ip);
    #-------------------------------------------------------------------------------------------------------------------------
    
    #Generate and save activation key
    #-------------------------------------------------------------------------------------------------------------------------
    $token = generate_activation_key($mailadresse);
    #-------------------------------------------------------------------------------------------------------------------------

    #If we didn't die() up to now, everything was successful
    
    #Send confirmation mail to user
    send_mail($mailadresse, "register", "http://newsletters.yacy-forum.de/activate", $mailadresse, $ip, $token);
    
    #Show success
    header("Location: success.html");
    
?>