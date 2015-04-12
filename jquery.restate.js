//RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi. 


//add a table
function tabellaAggiungi( )
{
var iId=  $( '#tabellaNome').val();
console.log( 'tabellaNome',iId );

$.ajax({
	url: './',
	type: 'PUT',
	dataType: 'json', // expected format for response
	//contentType: 'application/json', // send as JSON
	data: {name:iId,cmd:'tablecreate'},


	success: function(answer){
		//called when complete
		//alert('tablecreate',answer.result );
		//console.log('tablecreate',answer.result );
		window.location.reload();
	},

	error: function() {
		//called when there is an error
		console.log('error');
	},
});

return false;
}




function tableDrop()
{
$.ajax({
	url: "./",
	type: "delete",
	dataType: "json", // expected format for response
	//contentType: "application/json", // send as JSON
	data: {cmd:"tableDrop"},



	success: function(answer){
		//called when complete
		//	console.log("completed");
		alert("tableDropped",answer.result );
		//window.location.reload();
		window.location.replace("../"); //"http://repubblica.it"

	},

	error: function() {
		//called when there is an error
		console.log("error");
	},
});

return false;
}



function restateDelete( iId )
{
$.ajax({
	url: "./",
	type: "DELETE",
	dataType: "json", // expected format for response
	//contentType: "application/json", // send as JSON
	data: {id:iId},


	success: function(answer){
		//called when complete
		//	console.log("completed");
		//console.log("prenotazioneDelete",answer.result );
		//window.location.reload();
	},

	error: function() {
		//called when there is an error
		console.log("error on delete");
	},
});

return false;

}


function restateEdit( iId,iCol,sNewValue )
{
$.ajax({
	url: "./",
	type: "POST",
	dataType: "json", // expected format for response
	//contentType: "application/json", // send as JSON
	data: { "cmd":"tableUpdateNumber" ,"id":iId,"iCol":iCol,"sNewValue":sNewValue},


	success: function(answer){
		//called when complete
		//	console.log("completed");
		//console.log("prenotazioneDelete",answer.result );
		//window.location.reload();
	},

	error: function() {
		//called when there is an error
		console.log("error on delete");
	},
});

return false;

}



//new record snippet

function recordNew( jsonData )
{
//var iId=  $( "#idPrenotazione").val();
//console.log( "recordNew#idPrenotazione",iId );
//curl -d "cmd=bookprezzoupdate" -X POST  http://localhost/restateBB/availability/

$.ajax({
	url: "./",
	type: "PUT",
	dataType: "json", // expected format for response
	//contentType: "application/json", // send as JSON
	data: jsonData,


	success: function(answer){
		//called when complete
		//	console.log("completed");
		console.log("recordNew",answer.result );
		window.location.reload();
	},

	error: function() {
		//called when there is an error
		console.log("error");
	},
});

return false;
}

function showValues() {
//var str = $( "form" ).serialize();
var x= $( "form" ).serializeArray() ;

console.log( "form",x );
recordNew( x );

//$( "#results" ).text( str );
//return false;
}
//$( "input[type='checkbox'], input[type='radio']" ).on( "click", showValues );
//$( "select" ).on( "change", showValues );
//showValues();
