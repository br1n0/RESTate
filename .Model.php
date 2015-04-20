<?php //reSTATE a restful api mini framework. Written by Bruno Trombi. Released under the terms of the MIT license


///ispired by http://php.net/manual/en/function.is-numeric.php#107326
//try to convert value to number if it's rapresented in string in the same manner.
//this not happen for number that lead or end for zero like 01234 or 789.1000
function get_numeric($val) {

	if( is_numeric( $val ) )
	{
		//if $val seem numeric, is it converted into number: $added. 	
		$added= $val + 0;

		//if $added has same amount of chars the of $val, we return numerical version
		if( strlen( strval( $val ) ) == strlen( strval( $added ) ) )
			return $added;
	}

	return $val;
} 



  // Set default timezone
  date_default_timezone_set('UTC');

class Model extends PDO {

	private $sTable;

	public $status;
	public $msg;


//private $db= NULL;

/*
recreate the db skeleton
*/
function install( $fn )
{
	try
	{
    /**************************************
    * Create databases and                *
    * open connections                    *
    **************************************/
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO("sqlite:$fn");
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);

 
 
    /**************************************
    * Create tables                       *
    **************************************/
$sqlCreateTable = file_get_contents( ".model.default.sql" );
//!!! fixme, testare attentamente funziona la creazione quando il db e' vuoto!!!
//!!!deve creare le cartelle apposite

    	// Create table messages
    	$file_db->exec( $sqlCreateTable );
	}
	
	catch(PDOException $e)
	{
		// Print PDOException message
		echo $e->getMessage();
	}



}



function __construct( $sTable='' ,$fn= '.model.sqlite3' )
{

	$this->sTable= $sTable;


	if( !file_exists( $fn ) )
		$this->install( $fn );


	try //open the database (normal case)
	{
//		$this->open( $fn );
//	"sqlite:$fn"
//	    $file_db = new PDO("sqlite:$fn");
		parent::__construct( "sqlite:$fn" );
	}

	catch(Exception $e)
	{
		die( $this->getMessage() );
	}

$this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );


$this->restDie( 0 ,'' );

}



//per settare all'esterno i codici di errore
function restDie( $returnStatus ,$returnMsg )
{

	$this->status= $returnStatus;

if( empty($returnMsg)  )
	$this->msg= null;
else
	$this->msg= $returnMsg;

return null;
}




//dato un array di valori da inserire nella db
//fixme ritornare errori se il caso
function tableInsertArray( $table ,$input ) 
{
//return 42;

//se id=0 allora eliminiamo id, e proseguiamo rompendo i coglioni il meno possibile

if( array_key_exists( 'aValues' ,$input ) )
{
	$aValues= $input['aValues'];
}

$aValues[0]= null;

foreach( $aValues as $k => $v )
{
	$aAttributi2[]= ':'.$k;
}

$sAttributi2= implode(',', $aAttributi2);

	$sql= "INSERT INTO $table VALUES ($sAttributi2)";	
	$stmt = $this->prepare( $sql );


	//echo $sql;
foreach( $aValues as $k => $v )
{
	$stmt->bindValue(':'.$k, $v, PDO::PARAM_STR);
	//fixme, qui potrebbe bindare col parm_int parm_bool http://php.net/manual/en/pdo.constants.php
	//var_export( $stmt );
}
	$result = $stmt->execute();
return  $this->lastInsertId();

}





//fixme ritornare errori se il caso
function tableInsert( $table ,$input ) 
{

//se id=0 allora eliminiamo id, e proseguiamo rompendo i coglioni il meno possibile

if( array_key_exists( 'id' ,$input ) )
{
	if( 0 == intval( $input['id'] ) )
		unset($input['id'] );
}



//tra gli attributi mettiamo anche il rowid
$attributes_k= //array_merge( array( 'rowid' ),array_values( $this->tableDescribe( $table ) ) );
				array_values( $this->tableDescribe( $table ) );

$n= sizeof( $attributes_k ); 

$attributes_default=  array_pad( array(),$n,null);
$attributes= array_combine($attributes_k ,$attributes_default);

$input= array_intersect_key($input,$attributes );
$input= array_merge( $attributes,$input );

//print_r( $input );

$aAttributi= array();
$aAttributi2= array();
foreach( $input as $k => $v )
{
	$aAttributi[]= $k;
	$aAttributi2[]= ':'.$k;
}
$sAttributi= implode(',', $aAttributi);
$sAttributi2= implode(',', $aAttributi2);

	$sql= "INSERT INTO $table ($sAttributi) VALUES ($sAttributi2)";	

	$stmt = $this->prepare( $sql );

foreach( $input as $k => $v )
{
	$stmt->bindValue(':'.$k, $v, PDO::PARAM_STR);
	//fixme, qui potrebbe bindare col parm_int parm_bool http://php.net/manual/en/pdo.constants.php
}

	$result = $stmt->execute();

return  $this->lastInsertId();
}



//fixme ritornare errori se il caso
//fixme utilizzare bindvalue per evitare sqlinjection
function tableUpdate( $table ,$input )
{
	//$id= intval( @$input['id'] );
	$id= 0;
	if( array_key_exists( 'id' ,$input ) )
		$id= intval($input['id'] );


	unset( $input['id'] );
	if( $id<=0 ) 
		return $this->restDie( -6 ,"tableUpdate: id deve essere un numero non negativo" );

$tmp= $this->tableDescribe( $table );

//fixme sarebbe giusto prima trovare quale e' la sua  posziione e poi eliminarlo
unset( $tmp[0] ); //elimina rowid 

$attributes= array_flip( $tmp );


//print_r( $tmp );
//print_r( $attributes );

$data= array_intersect_key( $input ,$attributes );

//print_r( $data );
if( sizeof(  $data) <1 ) return null;

	$setStack= array();
	
	foreach( $data as $k => $v )
	{
//		$setStack[]= "$k='$v'" ;
		$setStack[]= "$k=:$k" ;
	}

	$set= ' SET ' .implode(',', $setStack);

	$sql= "UPDATE $table $set WHERE rowid=:id";
	//echo "\nsql:$sql\n";


	$stmt = $this->prepare( $sql );

	foreach( $data as $k => $v )
	{
		$stmt->bindValue(":$k", $v, PDO::PARAM_STR);
		//fixme qui sarebbe piu opportuno settare str,int, o float in base  a che dato sembra
	}
	$stmt->bindValue(":id", $id, PDO::PARAM_INT);

	$result = $stmt->execute();

return $id;
//return  $this->lastInsertId();
}





//fixme ritornare errori se il caso
//fixme utilizzare bindvalue per evitare sqlinjection
/*input solo k=v ex 13-2=pippo ovvero al record 13, colonna 2, aggiorna il valore a pippo
le chiavi codificate in urlencode non contengono il .
*/
function tableUpdateNumberCompact( $table ,$input )
{
unset($input['cmd']);

if( sizeof($input) != 1 )
		return $this->restDie( -61 ,"tableUpdateNumberCompact: esattamente un solo parametro accettato" );

foreach( $input as $kp => $v )
{

	list($id,$iCol)=explode("-", $kp);

	break;

}

$inputForward= array( 
	'id' => $id
	,'iCol'	=> $iCol
	,'sNewValue'=> $v
 );

//return "$id:$iCol:$v";


return $this->tableUpdateNumber( $table ,$inputForward );
}



//fixme ritornare errori se il caso
//fixme utilizzare bindvalue per evitare sqlinjection
function tableUpdateNumber( $table ,$input )
{
	$id= 0;
	if( array_key_exists( 'id' ,$input ) )
		$id= intval($input['id'] );

	unset( $input['id'] );
	if( $id<=0 ) 
		return $this->restDie( -6 ,"tableUpdateNum: id deve essere un numero non negativo" );



	$iCol= 0;
	if( array_key_exists( 'iCol' ,$input ) )
		$iCol= intval($input['iCol'] );
	else
		return $this->restDie( -7 ,"tableUpdateNum: iCol deve essere un numero non negativo" );


	if( array_key_exists( 'sNewValue' ,$input ) )
		$sNewValue= $input['sNewValue'];
	else
		return $this->restDie( -8 ,"tableUpdateNum: sNewValue deve essere valorizzato" );


	$aColumns= $this->tableDescribe( $table );
	$sColumn= $aColumns[$iCol];



	$sql= "UPDATE $table SET $sColumn=:sNewValue WHERE id=:id";
//	echo "\nsql:$sql\n";
	//return $sql;

	$stmt = $this->prepare( $sql );
	$stmt->bindValue(":sNewValue", $sNewValue, PDO::PARAM_STR);
	$stmt->bindValue(":id", $id, PDO::PARAM_INT);

	$result = $stmt->execute();

	return $id;
//return  $this->lastInsertId();
}







//column add to table 
// 
//curl -X PUT -d "cmd=tableColumnAdd&columnNameNew=pippo" http://localhost/restateBB/

function tableColumnAdd( $table ,$input )
{
//bar is the temporary table
$columnNameNew= "";
$columnNameValue= "1";

	if( array_key_exists( 'columnNameNew' ,$input ) )
		$columnNameNew= $input['columnNameNew'] ;
	else 
		return $this->restDie( -9 ,"tableColumnAdd: columnNameNew deve essere valorizzato" );

	if( array_key_exists( 'columnNameValue' ,$input ) )
		$columnNameValue= $input['columnNameValue'] ;


$schema= `echo .schema $table |sqlite3 .model.sqlite3`;
$r= $schema ."\n\n";

$renameTableToBar= `ALTER TABLE $table RENAME TO bar`;
$r.= "ALTER TABLE $table RENAME TO bar\n\n";

// non funzica perche non ho ricreato la tabella aggiungendo il nuovo attributo allo schema,
//bisogna inserire la nuova colonna prima dell ultimo ')' ed esguire;


//return  schema

$copyRowsFromBarToNewTable= `INSERT INTO $table   SELECT *,$columnNameValue as $columnNameNew FROM bar`;
$r.= "INSERT INTO $table   SELECT *,$columnNameValue as $columnNameNew FROM bar\n\n";

$removeTableBar= `DROP TABLE bar;`;
$r.= "DROP TABLE bar;\n\n";


return $r;

return "1";
}





//la delete e' il contrario
//si prende lo schema,
//va parsato togliendo il campo non piu necessario
//per semplicita supponiamo che ogni campo sia definito su una ed una sola riga
//(escluso id) da ,NomeCampo   in questo modo basta grep per eliminare abilitazioneMaestro
//echo .schema anagrafica  | sqlite3 .model.sqlite3 |grep abilitazioneMaestro -v

// si ricrea tabella, e si copiano i dati con una select in cui si elencano i campi
//si elimina la tabella

//curl -X PUT -d "cmd=tableColumnRemove&columnName=cf" http://localhost/restateBB/anagrafica
function tableColumnRemove( $table ,$input )
{
//bar is the temporary table
$columnName= "";

	if( array_key_exists( 'columnName' ,$input ) )
		$columnName= $input['columnName'] ;
	else 
		return $this->restDie( -10 ,"tableColumnRemove: columnName deve essere valorizzato" );


$renameTableToBar= "ALTER TABLE $table RENAME TO bar";
$schemaCreate=`echo .schema $table  | sqlite3 .model.sqlite3 |grep ,$columnName -v`;
//return $schemaCreate;

//$exec= `$renameTableToBar ;\n $schemaCreate | sqlite3 .model.sqlite3;

$columns=  $this->tableDescribe( $table );

$columnsTarget= implode( "," ,array_diff( $columns ,array( $columnName ) ) );

$sqlCopy = "INSERT INTO $table  SELECT $columnsTarget FROM bar";

//drop table bar;\n

$sqlCmd= "{$renameTableToBar};\n{$schemaCreate};\n{$sqlCopy};drop table bar;";


//$sqlCmdExec= str_replace( "\n","" ,$sqlCmd ) ;

//return $sqlCmdExec;
$this->exec( $sqlCmd );

return $sqlCmd;


$r= `$sqlCmd`;

return "r:$r";

}






//return array of attributes
//codice specifico sqlite
function tableDescribe( $table )
{
$r= //array('id');
array();
// fixme, dovremmo usare tutti per id invece che per rowid

$sql= "PRAGMA table_info([$table]);";
//echo "sql:$sql";

$result = $this->query( $sql , PDO::FETCH_NUM);

foreach($result as $row)
{
	$r[]= $row[1];
}
return $r;

/*
$sql= "select * from $table limit 1";

$result = $this->query( $sql );
$fields = array_keys($result->fetch(PDO::FETCH_ASSOC));

//return $fields;
return array_merge( array( 'rowid' ),array_values( $fields ) );
*/

}



/*
delete persona record,
!!! fix rowid to id
ritorna il numero di record modificati
*/
//function tableDelete( $table ,$id )
function tableDelete( $table ,$input )
{

//$id= intval( @$input["id"] );
$id= 0;
if( array_key_exists( 'id' ,$input ) )
	$id= intval($input['id'] );

if( $id<=0 )
	return $this->restDie( -5 ,"tableDelete: id deve essere un numero non negativo" );

$this->beginTransaction();
$sth = $this->prepare( "DELETE FROM $table WHERE rowid=:id" );
$sth->bindParam(':id', $id, PDO::PARAM_INT);
$sth->execute();

$iAffected = $sth->rowCount();

$this->commit();

return $iAffected;
}




//fixme escaping  $word
function tableAutocomplete( $table ,$input )
{

	if( !array_key_exists( 'column' ,$input ) )
		return $this->restDie( -3 ,"autocomplete: non hai specificato 'column' su cui effettuare il completamento automatico" );

	$r= array();
	//$word= @$input['word'];
	$word= null;
	if( array_key_exists( 'word' ,$input ) )
		$word= $input['word'] ;


	$column= $input['column'];

	//$tmp= intval( @$input['limit'] );
	$tmp= 0;
	if( array_key_exists( 'limit' ,$input ) )
		$tmp= intval($input['limit'] );


	if( $tmp <=0 )
		$limit= 10;
	else
		$limit= $tmp;
	//fixme se limit e' minore di zero andrebbe ritornato errore


	if( empty( $word ) )
		$WHERE= '';
	else
		$WHERE= "WHERE $column LIKE '$word%'";
//		$WHERE= "WHERE $column LIKE :word";

// SELECT DISTINCT nome FROM anagrafica where nome like 'a%'  order by 1 asc limit 10 ;
	$sql= "SELECT dISTINCT $column FROM $table $WHERE order by 1 asc limit $limit"; 

$sth = $this->prepare( $sql );

/*
$sth = $this->prepare( "DELETE FROM $table WHERE rowid=:id" );
$sth->bindParam(':id', $id, PDO::PARAM_INT);
$sth->bindParam(':id', $id, PDO::PARAM_INT);
$sth->execute();
$this->commit();
*/

	//if( strlen( $WHERE ) )
	//	$sth->bindParam(':word', $id, PDO::PARAM_STR);

	//echo "sql:$sql";

    $result = @$this->query( $sql , PDO::FETCH_NUM);
	//if( count($result)>0 )
    foreach($result as $row)
	{
		$r[]= $row[0];
	}

	//print_r( $r );

return $r;
}


//fixme questa non funziona con i filtri
function tableGetCount( $table ,$input=array() )
{
	$r= array();

    $result = $this->query( "SELECT COUNT(*) as count FROM $table"  , PDO::FETCH_NUM );
 
    foreach($result as $row)
	{
		$r[]= $row;
		break;
	}

	return $r[0][0];
}



//ritorna un solo record
//pars id=<NUM>
function tableGetOne( $table ,$input )
{
	//tra i parametri di inpu deve essere specificato id, che deve essere non negativo

	$id=0;
	if( array_key_exists( 'id' ,$input ) )
		$id= intval($input['id'] );


	if( $id <= 0 )
		return $this->restDie( -4 ,"tableGetOne: id deve essere un numero non negativo" );


	$input['limit']= 1;

	//implementare tramite limit e getall, 
	//il vantaggio e' non api piu omogenee e non dover riscrivere cose
	//$r= $this->tableGetAll( $table ,$input );


	$r= array();

	$query= "select * from $table where id=$id";
	//return $query;
    $result = $this->query( $query  , PDO::FETCH_NUM );
 
    foreach($result as $row)
	{
		$r[]= $row;
	}

return $r;

return $r[0];
}



/*
get, filtra su campo
get, filtra su tutti i campi

$bArrayResult indica se ciascun record viene rappresentato come un array o un dizionario,
nel caso array, i dati invece che sempre stringhe sono nel formatop piu compatto
invece di "42" si avra 42
*/
function tableGetAll( $table ,$input=array() )
{
	$r= array();


$bArrayResult=false;
if( array_key_exists( 'bArrayResult' ,$input ) ) $bArrayResult= true;


	$limit= 0;
	if( array_key_exists( 'limit' ,$input ) )
		$limit= intval($input['limit'] );
	

	//$limit= intval( $input['limit'] );
	if( $limit <=0 )
		$LIMIT= ''; //nessun limite
	else
		$LIMIT= "LIMIT $limit";

	$OFFSET= '';


	if( array_key_exists( 'page' ,$input ) )
		$page= intval($input['page'] );
	else 
		$page=0;

	//$page= intval( @$input['page'] );
	if( $page <=0 )
		$page= ''; //nessun limite
	else
	{
		if( $limit <=0 )
			return $this->restDie( -7 ,"tableGetAll: limit deve essere un numero >0" );

		$OFFSET= "OFFSET ".$page*$limit;
	}



	if( array_key_exists( 'orderBy' ,$input ) && !is_numeric( $input['orderBy'] ) )
			return $this->restDie( -8 ,"tableGetAll: orderBy deve contenere un numero" );


//	$orderBy= intval( @$input['orderBy'] );
	if( array_key_exists( 'orderBy' ,$input ) )
		$orderBy= intval($input['orderBy'] );
	else 
		$orderBy=0;



	if( $orderBy == 0 )
		$ORDERBY= 'ORDER BY 1 ASC';
	else
	{
		if( $orderBy >0 )
			$ORDERBY= "ORDER BY $orderBy ASC";
		else
			$ORDERBY= "ORDER BY " .abs($orderBy) ." DESC";

	}

//fixme filter where condition, vulnerabili a sql injection
//	print_r( $input['filter'] );
//	$filter= @$input['filter'];
	if( array_key_exists( 'filter' ,$input ) )
		$filter= $input['filter'] ;
	else 
		$filter=null;

	//fixme qui i campi accettati devono essere solo quelli definiti nella tabella	

	if( sizeof( $filter )<1  )
		$WHERE='';
	else
	{
		$whereStack= array();
		foreach( $filter as $k => $v )
			$whereStack[]= "$k like '%$v%'";
	

		$WHERE= 'WHERE '.implode(' AND ', $whereStack);
	}

	$sql= "SELECT * FROM $table $WHERE  $ORDERBY $LIMIT $OFFSET";
	if( $bArrayResult==false)
	{
		$result = $this->query( $sql , PDO::FETCH_ASSOC);

		foreach($result as $row)
		{
			$r[]= $row;
		}
	return $r;
	}

	else{
	    $result = $this->query( $sql , PDO::FETCH_NUM);
		foreach($result as $row)
		{
			$convertedRow= array();
			foreach( $row as $attr )
			{
				$convertedRow[]= get_numeric( $attr );
			}
		
			$r[]= $convertedRow;
		}
	return $r;
	}

return $r;
}


/*
cancella tabella ed anche la cartella assciata ed i file in essa contenuti
*/
function tableDrop( $table ,$input=array() )
{
//$table= "discipline";

//chdir("..");
$dir= `pwd`;
$x= `rm -rf $dir`;

$sqlTableDrop= "drop table {$table}";
$this->exec( $sqlTableDrop );

//return $sqlTableDrop;



return "dropped:$table.$dir";
}



function tableCreate( $table ,$input=array() )
{
$table=  $input['name'];
//return "t:$table.";

$r= `mkdir ./$table`;
`find -L ./ -xtype l | xargs -I {} cp {} ../$table`;

$sqlCreate= "CREATE TABLE {$table}
(
id INTEGER null PRIMARY KEY AUTOINCREMENT,
nome           TEXT    NOT NULL
)
";

$this->exec( $sqlCreate );

return "21";
}
















//dummy per scoprire le tablle 
function tableTables( $input=array() )
{
//return $this->exec( ".tables" );
$tables= `echo .tables |sqlite3 .model.sqlite3`;
$tables= explode("  ",trim($tables));
return $tables;
}



//dummy per scoprire la tabella corrente
function currentTable( $input=array() )
{
return $this->sTable;
}

//!!!! come mai
//curl 'http://localhost/backend/anagrafica/'  -f "cmd=tableUpdateNumberCompact&100-3=funzicato"

}//end class
?>
