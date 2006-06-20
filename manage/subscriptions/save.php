<?php
    
    include '../../functions.php';
    
    #-------------------------------------------------------------------------
    
    #Get mail
    $mailadresse=$_POST['mailaddress'];
    
    $mailadresse=trim($mailadresse);
    
    #Check email-address for @ and spam-attacks
    if (!preg_match("/^[\w.+-]{1,64}\@[\w.-]{1,255}\.[a-z]{2,6}$/",$mailadresse)){ 
           die($mailaddresse);
    }
    
    #Get the MD5-password out of the hidden input
    $password=$_POST['password'];
    
    #-------------------------------------------------------------------------
    
    #Which subscriptions are selected?
    if (empty($_POST['news'])) {
        $news= 0;
    } else {
    $news=1;
    }

    if (empty($_POST['security'])) {
        $security=0;
    } else {
    $security=1;
    }
    
    if (empty($_POST['release'])) {
        $release=0;
    } else {
    $release=1;
    }
    
    #-------------------------------------------------------------------------
    
    #Open MySQL-DB
    $link = open_db();
    
    #Save new subscription settings everything
    $sql="UPDATE User SET security_list = '$security', newsletter_list = '$news', announce_list = '$release' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse' AND CONVERT( md5_password USING utf8 ) = '$password'";

    mysql_query($sql) OR die(mysql_error());
    
    #close DB
    mysql_close($link);
    
    #-------------------------------------------------------------------------
    
    #Save the new modification time
    log_change($mailadresse, $password);
    
    #-------------------------------------------------------------------------
    
    #Show success of the subscription
    header("Location: success.html");

?>