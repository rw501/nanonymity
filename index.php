<html>
<head>
	<title>Nanonymity</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"></head>
<body>

<form id="order_form" action="javascript:void(0);">
  <div class="form-row">
    <div class="col">
      <div class="form-group">
        <input type="text" id="order_denom" class="form-control" placeholder="Denomination in NANO">
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <input type="text" id="order_deadline" class="form-control" placeholder="Time of Payout">
      </div>
    </div>
  </div>
  <div class="form-row">
    <div class="col">    
      <div class="form-group">
        <input type="text" id="order_dest" class="form-control" placeholder="Destination Address" value="nano_3k1iknyiuqegkbxxcmepptuez7nxu3w55natyzu9y74de9ukcaz7h74r7afq">
      </div>
    </div>
  </div>
  <input type="submit" id="order_submit" class="btn btn-primary" value="Place Order">
</form>


<div id="qrdiv" style="background:lightgray; width:360px; height:360px">
    <canvas id="qrcanv"}>No Canvas Support?
</div>


<?php
require_once( "util.php" );


$request = '{ "action": "block_count"}';
echo("Node Status: ");
ppr( rpc( $request, $node )."<br>" );


echo( "Mixing account: ".$mixing_account."<br>" );

$request = '{
    "action": "wallet_info",
    "wallet": "'.$wallet.'"
  }';
ppr( rpc( $request ) );



print_js_log();
?>


<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src='client_api.js'></script>
<script src='dev_api.js'></script>
<script src='qrenc3.js'></script>
</body>
</html>
