function api_generic( request ){
	$.post("api.php", request,
		function(data, status){
			if( status == "success" ) {
				console.log(data);
			} else {
				console.log("error:"+status);
			}
		
	});
}

function api_block_count(){
	api_generic( {"action": "block_count"} );
}

function api_account_create(){
	api_generic( { "action": "account_create", "wallet": "__wallet__"} );
}

function api_account_balance( account ){
	api_generic( { "action": "account_balance", "account": account} );
}
