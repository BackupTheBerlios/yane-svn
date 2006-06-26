<?php
    
    include '../../functions.php';
     
    #-------------------------------------------------------------------------
    
    #Check the email-address
    
    #get email-address from POST
    $mailaddress=$_POST['mailaddress'];
    
    $mailaddress=trim($mailaddress);
    
    #Check email-address for @ and spam-attacks
    if (!validate_email($mailaddress)){ 
    header("Location: email_wrong.html");
    die();
    }
    
    #-------------------------------------------------------------------------
    
    #Create random password (8 chars)
    $pattern = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $new_plainpw="";
    for($i=0;$i<8;$i++)
    {
      $new_plainpw .= $pattern{rand(0,61)};
    }
  
    $new_md5pw=md5($new_plainpw);
    
    #Open MySQL-DB
    $link = open_db();
    
    #To-DO: Check if user activated his account already
    
    #Save new random password
    $sql="UPDATE User SET md5_password = '$new_md5pw' WHERE CONVERT( email_address USING utf8 ) = '$mailaddress'";

    mysql_query($sql) OR die(mysql_error());
    
    #close DB
    mysql_close($link);
    
    #Send mail to user
    send_mail($mailaddress, "resend_password", $new_plainpw);
    
    #-------------------------------------------------------------------------
    
    #Show success of the subscription
    header("Location: success.html");

?>