reSTATE a restful api mini framework.
With kiss design, the target is develop restful api quickly, and mantain it without headache :)

It implement a rest api for access to a db (sqlite),
and expose a gui and api for crud app, and for modify the structure of db.

It accept urlencoded input, and answer in json.
Each answer include the requested url and method, with input, with timestamp,
because this way when a developer is looking for a bug he don't need to work blindly,
The overhead is negligible and the answer is gzipped when the client supports compression.

The current implentation use Sqlite, 
(because is loosely typed and don't offers useless authentication)
and Angular to provide a gui to create/delete/edit (double click for in cell editing)

e.g this is the answer of the api column for discover the columns
{
    "method": "get",
    "url": "http://localhost/rp/sample1/?cmd=columns",
    "tsRequest": 1428805112.471,
    "api-ver": 0.3,
    "input": {
        "cmd": "columns"
    },
    "result": [
        "id",
        "iTest",
        "sTest",
        "dTest"
    ],
    "status": 0,
    "msg": null
}


Somtimes in some api is not easy as should be, to identify which method is resposible for a bug in a known rest api.

in my implementation
each api is mapped to a folder, and heach folder contain 
.allowed.delete.php  
.allowed.get.php  
.allowed.options.php  
.allowed.post.php  
.allowed.put.php

inside you can find the mapping from api to exposed method.


