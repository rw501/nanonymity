<?php 

#####
##### CONFIG START
#####

$log_level = 2; ### 0 = none, 1 = ERROR, 2 = ERROR,SUCCESS; might be overwritten by POST requests (e.g. in client_api.php)

$node = 'http://127.0.0.1:7076';
$wallet = '25D6F6214830860A8B2577DD99B31E420AC9E6F555DCA5127975167A12E924EC';
$mixing_account_index = 0;
$current_epoch = time();

// connect to database
$db = new mysqli('localhost', 'nanonymity', 'Yfdb4p8kr2dSshMJ', 'nanonymity');
if ($db->connect_error) {
	log_danger( "Unable to connect to database: " . $db->connect_error );
} else {
	log_success( "Successfully connected to database..." );
}
if (TRUE !== $db->set_charset( "utf8mb4" ) ) {
	log_danger( "Unable to set Charset: $db->errno $db->error" );
};


#####
##### CONFIG END
#####

?>