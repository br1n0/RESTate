//RESTate a Mini framework for implement RESTFul statless APIs. Written by Bruno Trombi. 

var app = angular.module('app', []);
app.filter('filtraArray',filtraArray );


//app.controller('PersonCtrl', function customersController($scope,$http) {


function customersController($scope,$http) {

  $scope.bBoxInsertOpen= false;


$scope.actionBoxInsertOpen= function() {
$scope.bBoxInsertOpen= true;
}

$scope.actionBoxInsertClose= function() {
$scope.bBoxInsertOpen= false;
}



//class for sorting
$scope.sort=[];
//$scope.sort[0]= 'sort-false';

//sorting key
this.aSorting= [] //['-1'];


//set it for column filter
$scope.cercaColonna=[];


  $scope.this = this;
//console.log( $scope.this.aSorting ,"allo inizio" )


//trova tra gli $scope.records
$scope.trova = function ( idx )
{
	for (var i = 0; i < $scope.records.length; i++)
	{

		//console.log( "trova(" +idx +")",i,$scope.records[i][0]  )
		if ($scope.records[i][0]== idx )
		{
			return i;
		
		}
	}

	return -1;
}



$scope.editCell =function ( iId,iColumn )
{

var iPosRow= $scope.trova(  iId );
//console.log( "iPosRow",iPosRow );

//alert("edit:" +iPosRow +" column:" +iColumn);

//$scope.records[iPosRow][iColumn]= 99;
//myQuestion();

function myQuestion( question,value) {
    var newValue = prompt(question,value );
    
    if (newValue != null) {
        //document.getElementById("myQuestion").innerHTML =
        //"Hello " + newValue + "! How are you today?";
		//alert( newValue );

		$scope.records[iPosRow][iColumn]= newValue;

		//var iId= $scope.records[iPosRow][0]; // l'elemento alla posizione 0 e' l'id
	
		restateEdit( iId,iColumn,newValue )  //( iId,iCol,sNewValue )

    }
}

myQuestion( "cambua valore",$scope.records[iPosRow][iColumn] );

//$scope.records[iPosRow][iColumn]= "<input type='text' id='p{{i}}' size=7 value='codroipo'/>";

}

$scope.edit =function ( pos )
{
alert("edit:" +pos)
}




//remove data from window and db
$scope.remove = function (idx ) {
console.log( "remove" ,idx )

console.log( "records" ,$scope.records )

	var i= $scope.trova( idx );

console.log( "i" ,i )


	//se i<0 non esiste attualmente nel db
	if( i<0 ) return false; 

	//elimina dalla window, senza resettare :)
	$scope.records.splice(i,1);
	//$scope.aTd.splice(i,1);

	//elimina dal db, via chiamata rest
	restateDelete( idx ); 
}



function fChangeOrderNew(i) {
	//cambia ordine iesimo elemento
	//i e' 0 based, ma in js per fortuna c'e +0 che -0 :)

	var r;
	r=$scope.this.aSorting.indexOf( '-' +i, 0); 



	//changing the sort icon properly
	for( var j=0;j<=15;j++ )
	{
		$scope.sort[j] =''
	}

	if(r<0){
		$scope.this.aSorting= [ '-' +i]
		$scope.sort[i] ='sort-true'
	}
	else{
		$scope.this.aSorting= [ '+' +i]
		$scope.sort[i] ='sort-false'
	}
    //alert("fChangeOrderNew:" +$scope.this.aSorting);

 };

    $scope.changeOrder = fChangeOrderNew




$scope.getData = function(){

	if( $scope.recordHeads===undefined ){

		$http.get("./?cmd=columns&bJsonCompact=1")
		.success(function(response) {
			$scope.recordHeads = response.result;

			//console.log( $scope.recordHeads ,"recordHeads")

			//set defaults for cercaColonna[i]
			for( i= -1+$scope.recordHeads.length ;i>=0 ;i--)
			{
			$scope.cercaColonna[i]='';
			//$scope.cercaColonna[i]= $scope.recordHeads[i]
			//console.log( "col",$scope.cercaColonna[i],i,$scope.recordHeads[i])
			}

		});
	}



	$http.get("./?cmd=getAll&bArrayResult=true&bJsonCompact=1" )
	.success(function(response) {
		var i=0, j=0,row,td;
		var rows=  response.result;

		$scope.records = response.result;
	});
};


$scope.getData()
//setInterval($scope.getData, 1000);

$scope.changeOrder(0)

}

