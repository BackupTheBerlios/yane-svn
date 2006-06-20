<?php

    include 'config/access_data.php';
    
    function open_db()
    {
    $handler = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR die("Couldn't establish connection to database. Error: ".mysql_error());
    mysql_select_db(MYSQL_DATABASE) OR die("Couldn't use database. Error: ".mysql_error());
    return $handler;
    }
    
    function log_change($mailadresse, $pw)
    {
    $link = open_db();
    $time=date('Y-m-d H:i:s');
    $sql="UPDATE User SET last_change = '$time' WHERE CONVERT( email_address USING utf8 ) = '$mailadresse' AND md5_password = '$pw'";
    if (mysql_query($sql)) {
    mysql_close($link);
    return true;
    } else {
    return false;
    }
    }
    
    function show_version()
    {
    echo("Powered by <a href='http://developer.berlios.de/projects/yane/'>Yane</a> Ver. 0.4");
    }
    
?>