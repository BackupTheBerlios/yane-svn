<html>
    <head>
        <title>YaCy newsletter subscriptions</title>
    </head>
    <body>
        <center>
            <h2>YaCy newsletter subscriptions</h2>
            <br />
            <br />
            <br />
            <form method="POST" action="save.php">
                <table border="0" cellspacing="0" cellpadding="2" width="600">

<?php
    include '../../functions.php';
    
    #-------------------------------------------------------------------------
    
    $mailadresse=$_POST['mailaddress'];
    
    $mailadresse=trim($mailadresse);
    
    #Get the password
    $password=$_POST['password'];
    $password=md5($password);
    
    #Save email-address and password
    echo("<INPUT TYPE='HIDDEN' NAME='mailaddress' VALUE='$mailadresse'>");
    echo("<INPUT TYPE='HIDDEN' NAME='password' VALUE='$password'>");
    
    #-------------------------------------------------------------------------
    
    #Open MySQL-DB
    $link=open_db();

    #-------------------------------------------------------------------------
    
    #Which lists are selected?
    
    #security list?
    $result = mysql_query("SELECT * FROM User WHERE email_address = '$mailadresse' AND security_list = 1 AND md5_password = '$password';");
    if(mysql_num_rows($result) > 0) {
        $seclist = true;
    } else {
        $seclist = false;
    }
    
    #announce list?
    $result = mysql_query("SELECT * FROM User WHERE email_address = '$mailadresse' AND announce_list = 1 AND md5_password = '$password';");
    if(mysql_num_rows($result) > 0) {
        $ann_list = true;
    } else {
        $ann_list = false;
    }
    
    # newsletter list?
    $result = mysql_query("SELECT * FROM User WHERE email_address = '$mailadresse' AND newsletter_list = 1 AND md5_password = '$password';");
    if(mysql_num_rows($result) > 0) {
        $nl_list = true;
    } else {
        $nl_list = false;
    }
    
    #-------------------------------------------------------------------------
    
    #close DB
    mysql_close($link);
?>

                    <tr bgcolor="#bbcccc">
                        <td width="30"><input type="checkbox" name="news" value="news" valign="middle" id="check1" <?php if ($nl_list) { echo('checked'); } ?> /></td>
                        <td width="55"><img src="yacy.gif"></td>
                        <td valign="middle"><label for="check1"><font face="arial" size="5">-Newsletter</font></label></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2">
                            A <b>German</b> newsletter to keep you up to date. Low traffic.<br />
                            <br />
                            &emsp;
                        </td>
                    </tr>
                    
                    <tr bgcolor="#bbcccc">
                        <td><input type="checkbox" name="release" value="release" id="check2" <?php if ($ann_list) { echo('checked="true"'); }; ?> /></td>
                        <td><img src="yacy.gif"></td>
                        <td><label for="check2"><font face="arial" size="5">-release announcements</font></label></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2">
                            An <b>English</b> newsletter informing you of new YaCy stable-releases. Low traffic.<br />
                            <br />
                            &emsp;
                        </td>
                    </tr>
                    
                    <tr bgcolor="#bbcccc">
                        <td><input type="checkbox" name="security" value="security" id="check3" <?php if ($seclist) { echo('checked="true"'); } ?> /></td>
                        <td><img src="yacy.gif" align="bottom"></td>
                        <td><label for="check3"><font face="arial" size="5">-security announcements</font></label></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2">
                            An <b>English</b> newsletter informing you about security issues with YaCy and possible solutions. Very low traffic (we are proud of this ;-))<br />
                            <br />
                            &emsp;
                        </td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td colspan="2">
                            <input type="submit" name="save" value="Save" />
                        </td>
                    </tr>
                </table>
            </form>
        </center>
    </body>
</html>