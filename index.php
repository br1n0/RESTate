<?php //reSTATE a restful api mini framework. Written by Bruno Trombi. Released under the terms of the MIT license


error_reporting( E_ERROR );
function handleError($errno, $errstr,$error_file,$error_line ,$error_context)
{
global $test;
//echo "<br>ErrorLevel:$errno<hr>";
//echo "<b>Errore:</b> [$errno] $errstr - $error_file:$error_line\n<br>";
//var_export( $error_context );
//echo "<br />";
//echo "Terminating PHP Script";


global $data;
$data['msg']="errore amici miei";

$sData= restReturn( 17,$data ,$errno,$errstr,$error_file,$error_line );
$file = "../jErrors.txt";
file_put_contents($file, "\n" .$sData ,FILE_APPEND);



// the message
//$msg = "First line of text\nSecond line of text";
// use wordwrap() if lines are longer than 70 characters
//$msg = wordwrap($msg,70);

// send email, if not on localhost
if( $_SERVER['SERVER_NAME'] != 'localhost' )
	mail("bruno.trombi@gmail.com","bug:${data['url']}",$sData);


die('');

}
//set error handler
set_error_handler("handleError");





//json pretty print, se il php ce lo ha!
function json_encode_pretty($data)
{

//questo paramtro serve  a specificare, se vogliomo disabilitare il pretty print, per un piu compatto formato dati
$bJsonCompact= false;
if( array_key_exists( 'bJsonCompact' ,$data["input"] ) )
{
$bJsonCompact= $data["input"]["bJsonCompact"];
}





//se php veccio, non abbiamo pretty_print
if( intval( PHP_VERSION_ID ) > 50400 )
	if( $bJsonCompact == false )
		return json_encode($data ,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES); 
	else
		return json_encode($data ,JSON_UNESCAPED_SLASHES); 

//tophsot ha una versione piu vecchia e non supporta il pretty print
else
	return json_encode($data );
}



//start the buffer control, to prevent dirt into output
/*
ob_start();
serviamo la risposta compressa, se non puo, attiviamo il buffer normalmente
se e' riuscito ad attivare il buffer, setta l'header a gzip

*/
if(!ob_start("ob_gzhandler")) ob_start();
else
	header('Content-Encoding:gzip');



//versione

// PHP_VERSION_ID is available as of PHP 5.2.7, if our 
// version is lower than that, then emulate it
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

//$ver= PHP_VERSION_ID;





//$table= basename( getcwd() );
//global $table;
//echo "table:$table";

$serverName= $_SERVER['SERVER_NAME'];

$_METHOD= strtolower( $_SERVER['REQUEST_METHOD'] );
//$pathOrigin= getcwd();

//carichiamo il modello, ovvero l'ogetto che interagisce col db
//chdir("..");



//scopriamo su che tabella dobbiamo lavorare: dal .table.field  o dal penultimo ultimo elemento dell'url
//questo permette di poter diporre liberamente le api rest

//$table con il valore di .table.field (se esite)
if( file_exists( ".table.field" ) )
	$table= chop( file_get_contents( ".table.field" ) );

//se $table non ancora valorizzato, utilizza il nome della directory di livello superiore, 
//ex: da /pippo/pluto/antani/ ritorna pluto
if( empty( $table ) )
	$table= basename( getcwd() );


require_once( ".Model.php" );


$db = new Model( $table );
if(!$db) // fail fast: se qualcosa va male, esci subito
{
  echo $db->lastErrorMsg();
}
//chdir( $pathOrigin );

//exit(3);


//load requested method
//fixme cosa fare se il metodo manca
$data= array();


$data["method"]= $_METHOD;
$data["url"]=  $serverName. dirname( $_SERVER["SCRIPT_NAME"] ) .'/'; //a questo va tolto alla fine 'index.php'
//$data["cwd"]= getcwd();

//ricostruiamoci la url
$url= array(
'http://',
$_SERVER['SERVER_NAME']
);

if( $_SERVER['SERVER_PORT'] != 80 )
	$url[]= ':'.$_SERVER['SERVER_PORT'];

$url[]=$_SERVER['REQUEST_URI'];

$url= implode( $url,'' );

$data["url"]= $url;


$data["tsRequest"]= $_SERVER['REQUEST_TIME_FLOAT'];

$data["api-ver"]= 0.3;


$sQuery= $_SERVER["QUERY_STRING"];
$sBody= file_get_contents("php://input");





//per supportare internet explorer
//fixme supponiamo che i dati siano sempre urlencoded, se $_method==get via url, altrimenti nel body


if( !in_array( $_METHOD ,array( "get","post","put","delete","options" ) ) )
	$db->restDie( -99 ,"methods allowed must be get|post|put|delete" );

//la get prende i parametri da get, altrimenti da body
if( $_METHOD == "get" )
	parse_str($sQuery, $data["input"] );
else
	parse_str($sBody, $data["input"] );

require_once( ".allowed.${_METHOD}.php" ); //files starting with . are hidden in linux and not served outside.

$f= "${_METHOD}";




$data["result"]= $f( $db ,$table ,$data["input"] );
//$data= $f( $data["input"] );


/*!!!!
se ho catturato output tramite ob, significa che abbiamo errore server, per cui $data["status"] =1
se parametri sbagliati, status negativo

*/

//$data["msg"]= $db["msg"];
//$data["status"]= $db["status"]; 

//return $db->restDie( 99 ,"aaa" );

$tmp= $db->status;


$bufferOutput = ob_get_contents();
ob_clean();

if( strlen($bufferOutput) )
{
	$tmp=1; //roba sul buffer
	$db->msg= "errore: roba sul buffer";
	$data["bufferOutput"]= $bufferOutput;
}


restReturn( $tmp,$data );


function restReturn( $iTmp,$data ,$errono=null,$errstr=null,$error_file=null,$error_line=null )
{
global $db;

//set header
if( $iTmp == 0 ) header('HTTP/1.1 200 OK');
if( $iTmp < 0 )  header('HTTP/1.1 400 Bad Request');
if( $iTmp > 0 )  header('HTTP/1.1 500 Internal Server Error');


//set header mime type as json utf8
header('Content-Type: application/json; charset=UTF-8');

//accept cors call
header("Access-Control-Allow-Origin: *");


//no cache
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.


//con cache settata, da testare
//header('Cache-Control: private'); // HTTP 1.1.
//header('Expires: 999'); // Proxies.
//header('Etags: tie'); // Proxies.



$data["status"]= $iTmp; //msg errore per il programmatore!
$data["msg"]= $db->msg; //!!! utilizzare restdie e' meglio, e permette di eliminare questi parametri zozzi

if( $errono>0 ) $data["errono"]= $errono;
if( $errstr ) $data["errstr"]= $errstr;
if( $error_file ) $data["error_file"]= $error_file;
if( $error_line ) $data["error_line"]= $error_line;

/*
<0 500=>'500 Internal Server Error',
0= 200=>'200 OK',
>0 400=>'400 Bad Request',
*/

$r= json_encode_pretty($data);
echo $r;
return $r;


//send the data buffer to output
//ob_end_flush();
//echo "prima ha inviato:$bufferOutput\n";

}






?>
