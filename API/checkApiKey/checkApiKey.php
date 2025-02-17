<?php
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if(empty($apiKey)) {
    $error['ApiKey'] = ["You must provide an API key"];
    jsonResponse("Bad Request", 400, $error['ApiKey']);
}

?>