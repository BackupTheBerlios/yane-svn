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
    
    #Save new password
    update_pw($mailaddress, $new_plainpw);
    
    #Send mail to user
    send_mail($mailaddress, "resend_password", $new_plainpw);
    
    #-------------------------------------------------------------------------
    
    #Show success of the subscription
    header("Location: success.html");

?>