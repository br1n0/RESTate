// per filtrare un array di array, gli array che passano il filtro sono quelli che contengono alla pos la word passata
function filtraArray() {
    return function (items,sName) {

	var iLenTotal= 0;
	var aSearch= sName;

    for (var i = 0; i < sName.length; i++) {
        aSearch[i] = String( sName[i] ).toLowerCase();
		iLenTotal+= aSearch[i].length;
	}

	//console.log( "aSearch",aSearch ,'iLenTotal',iLenTotal )
	if( iLenTotal== 0 ) 
	{
		//console.log( "filter:empty" )
		//console.log( "aSearch",aSearch )
		return items;
	}

    var filtered = [];
    for (var j = 0; j < items.length; j++){
	    var item = items[j];
		var skipRow= false;

		for (var i = 0; i < aSearch.length; i++) {
			var str= String(  item[i] ).toLowerCase();

			var iPos= str.indexOf( aSearch[i] )
		    if( iPos <0 ){
				skipRow= true;
				//console.log( "skipped search" ,aSearch[i] ," ->" ,str  )
				break;
			}
		}

		if( skipRow === false )
			filtered.push(item)
	}

	return filtered;
    };
}

