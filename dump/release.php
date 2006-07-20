<?php

include '../functions.php';

$handler = open_db();

$sql = "SELECT email_address FROM Yane WHERE announce_list = 1";
    $result = mysql_query($sql) OR die(mysql_error());
    if(mysql_num_rows($result)) {
        while($row = mysql_fetch_assoc($result)) {
            echo ($row['email_address']);
            echo(",");
        }
    } else {
        echo"Noboy subscribed up to now...";
    }
    
    mysql_close($link);
?>
