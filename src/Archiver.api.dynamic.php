<?php
// Headers
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json");
header("Access-Control-Allow-Methods: post");

require_once "./classes/Archiver.php";

/*
|-----------------------------------------------------------------------------------------------------------------
| To call this API, here are the following keys:
|
| path:string (required) 
| passwordLength: int (optional) 
| add: array | string (required)
| store: string (required)
|-----------------------------------------------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    $files = $data->add ?? array();
    $Archiver = new Archiver($data->path ?? null);
    $Archiver->passwordLength = $data->passwordLength ?? 8;
    $Archiver->add($files);
    $Archiver->store($data->store);
} else {
    echo json_encode(array("status" => "failed", "message" => "Invalid HTTP Request"));
    http_response_code(400);
}
