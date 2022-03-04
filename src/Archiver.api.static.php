<?php
/*
* @Author: Kevin Jan Barluado 
* @Date: 2022-03-03 22:53:50 
* @Github: https://github.com/kevinjanbarluado2 
 */


// Headers
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json");

require_once "./classes/Archiver.php";

//Directory of the files that will be compressed to zip
$path = "C:/xampp2/htdocs/archiver/docs/";

$Archiver = new Archiver($path);
/*
|-----------------------------------------------------------------------------------------------------------------
| To use this API, here are the following keys:
|
| path: string (required) 
| add: array | string (required)
| savePath: string (required)
| passwordLength: int (optional)(default:8) 
| zipName: string (optional)(default:archived)
| secured: boolean (optional)(default:true)
|-----------------------------------------------------------------------------------------------------------------
/*
|-----------------------------------------------------------------------------------------------------------------
|   Uncomment to select password length 
|-----------------------------------------------------------------------------------------------------------------
*/
//$Archiver->passwordLength = 3;

/*
|-----------------------------------------------------------------------------------------------------------------
|   Uncomment to disabled password on zip
|-----------------------------------------------------------------------------------------------------------------
*/
//$Archiver->secured = false;


//User can add array
$Archiver->add(array("sample1.txt", "sample2.txt"));

//User can add string as well
$Archiver->add("sample3.txt");

//Stored in the path and it's filename 
$date = date("Ymd");
$Archiver->store('C:/xampp2/htdocs/Archiver/', "zipFile$date.zip");
