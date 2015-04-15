<?php //RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi.

// PHP_VERSION_ID is available as of PHP 5.2.7, if our 
// version is lower than that, then emulate it
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

$ver= PHP_VERSION_ID;



$content="";
$dirs= glob( "*" ,GLOB_ONLYDIR ) ;
//print_r( $dirs );
foreach( $dirs as $nameDir )
{
	$content.= "<tr><td><a href='./${nameDir}/' target='_blank'> {$nameDir} </a></td></tr>";
}


$fn= "model.png";
if( file_exists( $fn ) )
	$img= "<img alt='model' src='$fn'>";
else
	$img='';

echo "<!DOCTYPE html>
<html>
<head><title>RESTate</title>

<script src='./jquery.min.js'></script>
<script src='./jquery.restate.js'></script>

</head>
<body>
<h1>RESTate</h1>
<h5>Mini framework for implement REST APIs.
<br>Written by Bruno Trombi.<br>Released under the terms of the MIT license</h5>

<h6>
The REST architectural style describes six constraints. These constraints, applied to the architecture, were originally communicated by Roy Fielding in <a target='_blank' href='http://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm'>his doctoral dissertation</a>  and defines the basis of RESTful-style.
</h6>

<h4>Rest api available</h4>

<table border=0>

$content
</table>

$img

<hr>
<input type='text' size=20 id='tabellaNome' value='dummyTable' />
<button onclick='tabellaAggiungi();'>add a new table/api</button>


<hr>
<a target='_blank' href='./esempi.txt'><b>samples of how to call the api</a></a>
<br><br>
php version on server: $ver
</body></html>";
?>
