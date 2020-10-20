<?php

### TODO should not be accessble online

$json_return = TRUE;

require_once( 'util.php' );

$request = str_replace('__wallet__', $wallet, json_encode($_POST) );
$rpc_resonse = rpc( $request, $node );
echo( $rpc_resonse );

?>
