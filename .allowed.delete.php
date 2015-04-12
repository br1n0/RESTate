<?php //RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi. 


//these are the api accessible via delete, and corrispondent methods
//delete here is used for: delete some entities

function delete( $db ,$table ,$input )
{

	if( array_key_exists( 'cmd' ,$input ) )
	{
		//fixme fare confronto lovercase del cmd, per evitare stupidi problemi case sensitive
		$cmd= strtolower( $input['cmd'] );

		if( $cmd == 'tabledrop' )
			return $db->tableDrop( $table ,$input  );
	//	return "tableDrop( $table ,input  )";						

	}

return $db->tableDelete( $table ,$input );



}

?>
