<?php
    # Available fields in the database:
    # email_address   varchar(255)  	
    # md5_password   varchar(32)
    # security_list   	tinyint(1) 
	# announce_list  	tinyint(1)	  	
	# newsletter_list  tinyint(1) 
	# last_change  datetime
	# last_login  datetime 
	# last_login_ip  varchar(16) 
	# last_failure_ip varchar(16)
	# login_failures  int(11) 
	# account_locked_until datetime
	# activation_key  varchar(32)  

	require 'functions.php';
	
	#-------------------------------------------------------------------------------------------------------------------------
	
	#Open MySQL-DB
    $link = open_db();
	
	#Delete the old table
	mysql_query("DROP TABLE Yane");
	echo "Removed old table......... ";
	
	#The command to create the table
	mysql_query("
	CREATE TABLE Yane
(
    email_address varchar(255) character set utf8 default NULL,  	
    md5_password varchar(31) character set ascii default NULL,
    security_list tinyint(1) default 0,
	announce_list tinyint(1) default 0,  	
	newsletter_list tinyint(1) default 0,
	last_change datetime default NULL,
	last_login datetime default NULL,
	last_login_ip varchar(16) default NULL,
	last_failure_ip varchar(16) default NULL,
	account_locked_until datetime default NULL,
	login_failures int(11) default 0,
	activation_key varchar(32) character set ascii default NULL
)
") OR die("Couldn't create new Yane table:" . mysql_error());
	
	echo "Created the new table........... ";
	
	#Close the DB
	mysql_close($link);
	
	echo "Done.";
?>
