<?php //RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi. 


//these are the api accessible via post, and corrispondent methods
//post here is used for: update recrord

function post( $db ,$table ,$input )
{

	if( array_key_exists( 'cmd' ,$input ) )
	{
		//fixme fare confronto lovercase del cmd, per evitare stupidi problemi case sensitive
		$cmd= strtolower( $input['cmd'] );
		//$cmd= $input['cmd'];		


		//change a value
		if( $cmd == 'tableupdatenumber' )
			return $db->tableUpdateNumber( $table ,$input );

		//change a value, a more compact parameters version.
		if( $cmd == 'tableupdatenumbercompact' )
			return $db->tableUpdateNumberCompact( $table ,$input );
	
	
	}



return $db->tableUpdate( $table ,$input );
}
?>
