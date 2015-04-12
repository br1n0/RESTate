<?php //RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi. 


//these are the api accessible via get, and corrispondent methods
//get here is used for: retrive data, btw all infos are cacheable and each one don't change any data

function get( $db ,$table ,$input )
{

	if( array_key_exists( 'cmd' ,$input ) )
	{
		//fixme fare confronto lovercase del cmd, per evitare stupidi problemi case sensitive
		$cmd= strtolower( $input['cmd'] );
		//$cmd= $input['cmd'];		
	

		if( $cmd == 'gettable' )
			return $table;			

		if( $cmd == 'currenttable' )
			return $db->currentTable( );		



		if( $cmd == 'getall' )
			return $db->tableGetAll( $table ,$input  );			

		//?cmd=getOne&rowid=99
		if( $cmd == 'getone' )
			return $db->tableGetOne( $table ,$input  );			

		if( $cmd == 'columns' )
			return $db->tableDescribe( $table  );

		//?cmd=count
		if( $cmd == 'count' )
			return $db->tableGetCount( $table ,$input );

		//?cmd=autocomeplete&column=nome&word=pi &limit=10
		if( $cmd == 'autocomplete' )
			return $db->tableAutocomplete( $table ,$input );



		if( $cmd == strtolower('availability') ){
//			print_r( $input );
			$from= strtolower( @$input['from'] );
			$till= strtolower( @$input['till'] );
//			return 69;
			return $db->getAvabilityBetween( $from ,$till );
		}
			

		//if( $cmd == strtolower('getAllArray') )
		//	return $db->tableGetAll( $table ,$input ,true ) ;


		if( array_key_exists( 'cmd' ,$input ) )
			return $db->restDie( -1 ,"get.parametro cmd: funzione corrispondente al parametro cmd non trovata" );
	}

	//return $db->tableGetAll( $table ,$input ) ;
	

	
	$f= "gui.php"; //se questo presente, servilo e termina
	if( file_exists( $f ) )
	{
		include( $f );
		die( "");
	}

	$f= "gui.html";//se questo presente, servilo e termina
	if( file_exists( $f ) )
	{
		include( $f );
		die( "");
	}



}


?>
