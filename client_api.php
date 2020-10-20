<?php

### accessed via ajax post; return in JSON only
require_once( 'util.php' );

### functions
function place_order( $denom, $dest, $deadline ){
	global $current_epoch;
	$account = account_create(); ### TODO error handling
	$query = "INSERT INTO `orders` (`account`, `denomination`, `submission_epoch`, `fulfillment_account`, `fulfillment_deadline_epoch`) 
			VALUES ( '$account', $denom, $current_epoch, '$dest', $deadline )";
	sql_query( $query );
	return $account;
}

// check when/if fully_received is set for given account/order
function fully_received_check( $account ){
	$fully_received = false;
    $query = "SELECT `order_id`, `account`, `fully_received_epoch` FROM `orders` WHERE `account` = '".$account."' AND `fully_received_epoch` IS NOT NULL";
	$result = sql_query( $query );
	if ( $result->num_rows == 1 ) $fully_received = true;    
    return $fully_received;
}

### TODO sanitize data

$response = '{ "response": "none" }';


switch( $_POST['action'] ){
	case 'block_count':
		$request = '{"action": "block_count"}';
		$response = rpc( $request );
		break;
	case 'place_order':
		$denom = mnano_to_raw( $_POST['denom'] ); # $denom = raw as string # TODO error handling, check for valid denomination
		if( $_POST['deadline'] > $current_epoch ) $deadline = $_POST['deadline'];
		else $deadline = $current_epoch;
		$dest = $_POST['dest']; # TODO error handling, check for valid nano address
		$account = place_order( $denom, $dest, $deadline );
		$response = '{ "account": "'.$account.'", "denom": "'.$denom.'", "deadline": "'.$deadline.'" }';
		break;
	case 'fully_received_check': # returns boolean for fully_received
		$account = $_POST['account'];
		$fully_received = fully_received_check( $account );
		$response = '{ "fully_received": "'.$fully_received.'" }';
		break;
	default: 
		$request = exit($node);
	
}

if ( $global_error === true ) $response = '{ "error": "There has been an error" }'; # give error feedback, without exposing the error, recommended for production
echo( $response );


$log_level = 1; ### 0 = none, 1 = ERROR, 2 = ERROR,SUCCESS
print_js_log();
?>
