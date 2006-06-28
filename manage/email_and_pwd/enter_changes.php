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
    
    $plain_pw=$_POST['password'];
    
    #-------------------------------------------------------------------------

?>

<html>
    <head>
        <title></title>
    </head>
    <body>
        <center>
            <br />
            <br />
            <br />
            <form method="POST" action="save.php">
            Old password: <?php echo("<INPUT NAME='old_password' VALUE='$plain_pw'>"); ?> <br>
            Old email-address: <?php echo("<INPUT NAME='old_mailaddress' VALUE='$mailaddress'>");?> <p>
            
            You can change your email-address/your account password here.<br>
            If you leave a field blank, the old information will be left unchanged.<p>
            
            New password: <INPUT NAME='new_password1' VALUE=''> <br>
            Repeat new password: <INPUT NAME='new_password2' VALUE=''> <br>
            New email-address: <INPUT NAME='new_mailaddress' VALUE=''> <br>
            </form>
        </center>
    </body>
</html>