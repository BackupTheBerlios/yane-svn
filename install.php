<?php
    #Available fields in the database:
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
    

	require 'functions.php';
	
	#-------------------------------------------------------------------------------------------------------------------------
	
	#Open MySQL-DB
    $link = open_db();
	
	#The command to create the table
	mysql_query("
	CREATE TABLE Yane
(
    email_address varchar(255),  	
    md5_password varchar(32),
    security_list tinyint(1),
	announce_list tinyint(1),  	
	newsletter_list tinyint(1),
	last_change datetime,
	last_login datetime,
	last_login_ip varchar(16),
	login_failures int(11),
	activation_key varchar(12)
)
") OR die(mysql_error());
	
	#Close the DB
	mysql_close($link);
?>
CREATE TABLE Yane (
  email_address varchar(255) character set utf8 default NULL,
  md5_password varchar(31) character set ascii default NULL,
  security_list tinyint(1) default NULL,
  announce_list tinyint(1) default NULL,
  newsletter_list tinyint(1) default NULL,
  last_change datetime default NULL,
  last_login datetime default NULL,
  last_login_ip varchar(16) character set ascii default NULL,
  login_failures int(11) default NULL,
  activation_key varchar(31) character set ascii default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
