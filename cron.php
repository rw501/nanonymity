<?php

require_once( "util.php" );

// check for new received transactions for open orders (orders that have not fully received the required denomination)
function fully_received_update( $account = NULL, $recent_only = TRUE ){
    global $current_epoch;
    $recent_unix = $current_epoch - (7 * 24 * 60 * 60); # - (7 * 24 * 60 * 60) == last week
    $query = "SELECT `order_id`, `account`, `denomination` FROM `orders` WHERE `fully_received_epoch` IS NULL"; #TODO expire orders/move to different table?; to check only for missed/late orders: AND `fulfillment_deadline_epoch` <= ".$current_epoch
    if( $account !== NULL ) $query .= " AND `account` = '".$account."'";
    if( $recent_only === TRUE ) $query .= " AND `submission_epoch` > '".$recent_unix."'";
    $result = sql_query( $query );
    $rows = $result->fetch_all( MYSQLI_ASSOC );
    foreach( $rows as $row ){ # foreach pending order check if denomination has been reached
        $response = get_confirmed_balance( $row['account'] );
        if( isset( $response['balance'] ) && $response['balance'] >= $row['denomination'] ){ # balance matches denomination, confirm received in db
            log_success( $row['account'].": confirmed balance matches the denomination" );
            $query = "UPDATE `orders` SET `fully_received_epoch` = '".$current_epoch."' WHERE `account` = '".$row['account']."'";
            sql_query( $query );
        }
        else if( isset( $response['error'] ) ) log_danger( $row['account'].": ".$response['error'] );
        else log_danger( $row['account'].": confirmed balance does not match the denomination" );
    }
}

// create mixing transaction: move funds from unique account to mixing account
function mix_fully_received( $account = NULL ){
    global $current_epoch;
    $query = "SELECT `order_id`, `account` FROM `orders` WHERE `fully_received_epoch` IS NOT NULL AND `mixer_tx` IS NULL"; #TODO to check only for missed/late orders: AND `fulfillment_deadline_epoch` <= ".$current_epoch
    if( $account !== NULL ) $query .= " AND `account` = '".$account."'";
    $result = sql_query( $query );
    $rows = $result->fetch_all( MYSQLI_ASSOC );
    foreach( $rows as $row ){ # foreach fully_received order send all funds to mixing account
        $response = create_mixing_tx( $row['account'] );
        if( isset( $response['error'] ) ) log_danger( $row['account'].": ".$response['error'] );
        else if ( $response["block"] != "" ) {
            $block = $response["block"];
            $query = "UPDATE `orders` SET `mixer_tx` = '".$block."', `mixer_epoch` = '".$current_epoch."' WHERE `account` = '".$row['account']."'"; # TODO set mixer_epoch only after check for network confirmation
            sql_query( $query );
        } else log_danger( $row['account'].": mixing was not succesfull" );
    }
}

// payout mixed funds to destination address
function fulfill_mixed( $account = NULL ){
    global $current_epoch, $mixing_account;
    $query = "SELECT `order_id`, `account`, `denomination`, `fulfillment_account` FROM `orders` WHERE `mixer_tx` IS NOT NULL AND `fulfillment_tx` IS NULL"; #TODO to check only for orders with minimum amount of mixing time/transactions
    if( $account !== NULL ) $query .= " AND `account` = '".$account."'";
    $query .= " ORDER BY `fulfillment_account`"; # randomize payout
    $result = sql_query( $query );
    $rows = $result->fetch_all( MYSQLI_ASSOC );
    foreach( $rows as $row ){ # foreach mixed order send denominated funds to destination_account
        $response = create_send_tx( $mixing_account, $row['fulfillment_account'], $row['denomination'] );
        if( isset( $response['error'] ) ) log_danger( $row['account'].": ".$response['error'] );
        else if ( isset( $response["block"] ) && $response["block"] != "" ) {
            $block = $response["block"];
            $query = "UPDATE `orders` SET `fulfillment_tx` = '".$block."', `fulfillment_epoch` = '".$current_epoch."' WHERE `account` = '".$row['account']."'"; # TODO set fulfillment_epoch only after check for network confirmation
            sql_query( $query );
            log_success( $row['account'].": fulfillment completed" );
        } else log_danger( $row['account'].": fulfillment was not succesfull" );
    }
}


echo( "Mixing account: ".$mixing_account."<br>\n" );

$request = '{
    "action": "wallet_info",
    "wallet": "'.$wallet.'"
  }';
ppr( rpc( $request ) );


$shortopts  = "ca::"; #c = check_only (node readonly), a(optional) = address to check for
$options = getopt( $shortopts );
#var_dump($options);

if ( isset( $options["a"] ) ) $account = $options["a"];
else $account = null;

### execute cronjobs
if ( !isset( $options["c"] ) ){ #don't execute, if -c ( check_only, node readonly ) option was passed
    fulfill_mixed( $account );
    mix_fully_received( $account );
}
fully_received_update( $account ); #nano_1dzrnstyiz8hqcubh7qbpfmu1iw9gfdsif3qf6wmyiumiahqsz3cadkb3ujy

if( isset($argv) ) print_cli_log();
else print_js_log();

?>
