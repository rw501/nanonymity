function client_api_generic( request ){
	$.ajax({
		type: "POST",
		url: "client_api.php",
		data: request,
		dataType: "json",
		success: function(result, status){
			if( status == "success" ) {
				console.log(result);
				if( result.account && result.denom) doqr( "nano:"+result.account+"?amount="+result.denom );
			} else {
				console.log("error:"+status);
			}
		}
	  });
}

function client_api_block_count(){
	client_api_generic( {"action": "block_count"} );
}

function client_api_place_order( denom, dest, deadline = null ){ // denom in NANO aka Mnano
	client_api_generic( {"action": "place_order", "denom": denom, "dest": dest, "deadline": deadline} );
}

function client_api_fully_received_check( account ){
	client_api_generic( {"action": "fully_received_check", "account": account} );
}

$("#order_submit").click( function(){
	client_api_place_order( $("#order_denom").val(), $("#order_dest").val(), $("#order_deadline").val() );
});