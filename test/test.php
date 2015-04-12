<?php //questo script permette di verificare il risultato di una chiamata rest

function die2( $str ,$rv=0 )
{
global $resultExpected;
global $resultRest;
global $sData;

	echo $str ."\n";

	if( $rv==0 )
	{
		echo "atteso:\n"
			.json_encode( $resultExpected,JSON_PRETTY_PRINT )
			."\n\nritornato:\n"
			.json_encode( $resultRest,JSON_PRETTY_PRINT )
		
			."\n\nritornato raw:\n"
			.$sData
		;
	}
	exit( $rv );
}

$fn= $argv[1];

if( !file_exists( $fn ) )
	die2( "il file non si apre!" );

$jData= json_decode( file_get_contents( $fn ) ,true ); 
if(json_last_error() != JSON_ERROR_NONE)
	die2( "error on json test!" );

$resultExpected= $jData['result'];
$url= $jData['url'];

$sData= @file_get_contents( $url );
//var_export( $http_response_header );

list( $httpVer,$statusCode )= sscanf($http_response_header[0], "%s %f %d");


//echo "\nASPETTAVO:" .$jData['httpStatusCode'] .".\n";

if( @!empty($jData['httpStatusCode']) && $jData['httpStatusCode']!=$statusCode )
die2( "ERRORE:$fn,\n  httpStatusCode:" .$statusCode ." invece di:" .$jData['httpStatusCode'] ."\n" );



$jData= json_decode( $sData ,true ); 
if(json_last_error() != JSON_ERROR_NONE)
	die2( "error json or rest result!, is not json! on $fn" );

$resultRest= $jData['result'];
if( is_array( $resultExpected ) )
{
	foreach( $resultExpected as $k => $v )
		if( @$resultRest[$k]!=$v ) 
			die2( "DIVERSO DA ATTESO:" .$v ." invece di:" .$resultRest[$k] ."\n" );

die2( "ok:$fn" ,1 );
}


if( $resultExpected != $resultRest )
	die2( "DIVERSO DA ATTESO:" .$resultExpected ." invece di:" .$resultRest ."\n" );

die2( "ok:$fn" ,1 );
?>
