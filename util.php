<?php

require_once("config.php");

#####
##### GLOBAL VARS
#####

$logs; # $logs[ uint $index ][ string "SUCCESS"|"ERROR" ][ string $msg ]
$global_error = false; # boolean, is set to true when an error occurs
$mixing_account = get_account_by_index( $mixing_account_index );

#####
##### GLOBAL VARS END
#####

#####
##### UTILITY FUNCTIONS	
#####
function rpc( $request ){
	global $node;
	$ch = curl_init($node);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($request)
    ));
    
    $curl_result = curl_exec($ch);
    
	// connection erros go here: 
    if (curl_errno($ch)) {
        log_danger( 'Curl error while trying to reach node: ' . curl_error($ch) . '' );
    } else {
		// results and node errors go here
        return $curl_result;
    }
}

function sql_query( $query ){
	global $db;
	$result = $db->query( $query ) or log_danger( $query.": ".$db->error );
	if( $result !== TRUE ) { // only return if $result has content
		return $result;
	}
}

function log_danger( $msg ){
	global $logs, $global_error, $db;
	$global_error = true;
	$msg = $db->real_escape_string( $msg );
	$logs[] = array( "type"  => "ERROR", "msg" => $msg );
}

function log_success( $msg ){
	global $logs, $db;
	$msg = $db->real_escape_string( $msg );
	$logs[] = array( "type"  => "SUCCESS", "msg" => $msg );
}

function print_js_log(){
	global $log_level, $logs;
	foreach( $logs as $log){
		if( $log["type"] == "ERROR" && $log_level >= 1)			echo( "<script> console.error('".$log["msg"]."'); </script>" );
		else if( $log["type"] == "SUCCESS" && $log_level >= 2)	echo( "<script> console.info('".$log["msg"]."'); </script>" );
	}
}

function print_cli_log(){
	global $log_level, $logs;
	foreach( $logs as $log){
		if( $log["type"] == "ERROR" && $log_level >= 1)			echo( "[ERROR]".$log["msg"]."'\n" );
		else if( $log["type"] == "SUCCESS" && $log_level >= 2)	echo( "[SUCCESS]".$log["msg"]."\n" );
	}
}

function pretty_print_r( $array ){
	global $argv;
	if( !isset($argv) ) echo "<pre>";
	print_r( $array );
	if( !isset($argv) ) echo "</pre>";
}

function ppr( $array ) { 
	pretty_print_r( $array );
}

function mnano_to_raw( $string ){ # convert NANO aka Mnano to raw; returns raw as string
	$string = str_replace( ",", ".", $string );
	$point_pos = strpos( $string, ".");
	if( $point_pos !== false ) $amount_decimals = strlen( $string ) - $point_pos - 1;
	else $amount_decimals = 0;
	$trailing_zeros = 30 - $amount_decimals;
	$string_expanded = $string;
	for( ; $trailing_zeros > 0; $trailing_zeros-- ) $string_expanded.="0";
	$raw_string = str_replace( ".", "", $string_expanded );
	$raw_string = ltrim( $raw_string, "0" ); # remove leading zeroes
	#$raw = gmp_init( $raw_string );
	return $raw_string;
}
#####
##### UTILITY FUNCTIONS	END
#####

#####
##### NODE FUNCTIONS
#####
function account_create(){
	global $wallet;
	$request = '{
		"action": "account_create",
		"wallet": "'.$wallet.'"
	}';
	$response = rpc( $request );
	$account = json_decode( $response, true )["account"];
	return $account;
}

function create_send_tx( $source, $dest, $amount = NULL ){
	global $wallet;
	$request = '{
		"action": "send",
		"wallet": "'.$wallet.'",
		"source": "'.$source.'",
		"destination": "'.$dest.'",
		"amount": "'.$amount.'"
	}'; # TODO set send id, save in db
	$response = rpc( $request );
	$decoded = json_decode( $response, true );
	return $decoded;
}

function get_confirmed_balance( $account ){
	$request = '{
		"action": "account_info",
		"account": "'.$account.'"
	}';
	$response = rpc( $request );
	$decoded = json_decode( $response, true );
	if( isset( $decoded['confirmation_height'] ) && $decoded['confirmation_height'] !== $decoded['block_count'] )  $return = array( "error"  => "unconfirmed" ); # IMPORTANT, only accept confirmed balances!!!
	else if ( isset($decoded['balance'] ) ) $return['balance'] = $decoded['balance'];
	else $return['error'] = $decoded['error'];
	return $return;
}

function get_account_by_index( $index ){
	global $wallet;
	$request = '{
		"action": "account_create",
		"wallet": "'.$wallet.'",
		"index": "'.$index.'",
		"work": "false"
	}';
	$response = rpc( $request );
	$account = json_decode( $response, true )["account"];
	return $account;
}

function create_mixing_tx( $account ){ // send all funds of given account to mixing account
	global $wallet, $mixing_account;
	$balance = get_confirmed_balance( $account )['balance'];
	log_success( $account.": mixing ".$balance." raw" );
	$request = '{
		"action": "send",
		"wallet": "'.$wallet.'",
		"source": "'.$account.'",
		"destination": "'.$mixing_account.'",
		"amount": "'.$balance.'"
	}'; # TODO set send id, save in db
	$response = rpc( $request );
	$decoded = json_decode( $response, true );
	return $decoded;
}
#####
##### NODE FUNCTIONS END
#####

?>
