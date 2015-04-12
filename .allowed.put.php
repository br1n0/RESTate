<?php //RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi. 

//these are the api accessible via put, and corrispondent methods
//put here is used for: insert new entities, e.g. a new record

function put( $db ,$table ,$input )
{

	if( array_key_exists( 'cmd' ,$input ) )
	{
		//fixme fare confronto lovercase del cmd, per evitare stupidi problemi case sensitive
		$cmd= strtolower( $input['cmd'] );

		if( $cmd == 'tablecreate' )
			return $db->tableCreate( $table ,$input  );
	//	return "tableCreate( $table ,input  )";						
	
		if( $cmd == 'tablecolumnadd' )
				return $db->tableColumnAdd( $table ,$input  );


		if( $cmd == 'tablecolumnremove' )
				return $db->tableColumnRemove( $table ,$input  );
		

//		if( $cmd == 'tableinsertarray' )
//				return $db->tableInsertArray( $table ,$input  );		

	}


return $db->tableInsert( $table ,$input  );
//return $db->tableInsertArray( $table ,$input  );
//return 9;


}

?>
