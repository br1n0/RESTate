RESTate is a restful api mini framework.
With kiss design, the target is develop restful api quickly, and mantain it without headache :)

It implement a rest api for access to a db (sqlite),
and expose a gui and api for crud app, and for modify the structure of db.

It accept urlencoded input, and answer in json.
Each answer include the requested url with http method, with input, and timestamp,
because this way when a developer is looking for a bug he don't need to work blindly,
in case of server error, also a description of error and line number is reported.
The overhead on network is negligible and the answer is gzipped when the client supports compression.

The current implentation use Sqlite, 
(because is loosely typed and don't offers useless authentication)
and Angular to provide a gui to create/delete/edit (double click for in cell editing)

e.g this is the answer of the api column for discover, named: columns
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


Somtimes in some framework the api is not easy as should be, and routing don't help to simplify the loking for which method is resposible for a bug, given an api with url, you must read some code to understand the mapping of function.

in my implementation I decided to use directory to map url, 
so them are static by design and not by rewrite,

each api is mapped to a directory, and each directory contain 
.allowed.delete.php  
.allowed.get.php  
.allowed.options.php  
.allowed.post.php  
.allowed.put.php

inside of them you can find the mapping of exposed method.

On each folder index.php is resposable of get input, execute code, return output to the client.
index.php is served when you ask for a url that is a directory, so using directory url them are looking as static url,
I preferred url that seems directory instead of file because directory could contain also files,
so could store some data on his directory, so each api could be well isolated.

This architecture could lead a files proliferation, and lead to a mantening nigthmare, you may think, 
but I solved this using the force of symbolic files,
This way .allowed.get.php contain the same code of test/.allowed.get.php, but if you want different things you can.






