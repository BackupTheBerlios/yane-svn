<?php
    
    include '../../functions.php';
     
    #-------------------------------------------------------------------------
    
    #Open MySQL-DB
    $link = open_db();
    
    #Save new subscription settings everything
    $sql="UPDATE User SET security_list = '$security', newsletter_list = '$news', announce_list = '$release' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse' AND CONVERT( md5_password USING utf8 ) = '$password'";

    mysql_query($sql) OR die(mysql_error());
    
    #close DB
    mysql_close($link);
    
    #-------------------------------------------------------------------------
    
    #Show success of the subscription
    header("Location: success.html");

?>