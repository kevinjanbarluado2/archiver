<?php
/*
* @Author: Kevin Jan Barluado 
* @Date: 2022-03-03 22:53:50 
* @Github: https://github.com/kevinjanbarluado2 
 */
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json");
header("Access-Control-Allow-Methods: post");

require_once "./classes/Archiver.php";
/*
|-----------------------------------------------------------------------------------------------------------------
| To call this API, here are the following keys:
|
| path: string (required) 
| add: array | string (required)
| savePath: string (required)
| passwordLength: int (optional)(default:8) 
| zipName: string (optional)(default:archived)
| secured: boolean (optional)(default:true)
|-----------------------------------------------------------------------------------------------------------------
*/
$status = fn ($status, $message) => json_encode(array("status" => $status, "message" => $message));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $files = $data->add ?? array();
    $Archiver = new Archiver($data->path ?? null);
    $Archiver->passwordLength = $data->passwordLength ?? 8;
    $Archiver->add($files);
    $Archiver->secured = $data->secured ?? true;
    if (empty($data->savePath)) {
        echo $status("error", "Undefined path to save file");
        http_response_code(404);
        die();
    }
    $Archiver->store($data->savePath, isset($data->zipName) ? trim($data->zipName) : null);
} else {
    echo $status("error", "Invalid HTTP Request");
    http_response_code(400);
}
