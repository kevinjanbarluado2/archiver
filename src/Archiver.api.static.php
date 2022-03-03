<?php
// Headers
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json");
header("Access-Control-Allow-Methods: post");

require_once "./classes/Archiver.php";

//Directory of the files that will be compressed to zip
$path = "C:/xampp2/htdocs/archiver/docs/";

$Archiver = new Archiver($path);

//Uncomment to choose password length 
//$Archiver->passwordLength = 3;

$Archiver->add(array("sample1.txt", "sample2.txt"));
$Archiver->add("sample3.txt");

$Archiver->store('C:/xampp2/htdocs/Archiver/zipped.zip');
