<?php
require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

parse_str(file_get_contents('php://input'), $data);
$movieID = isset($data['movieID']) ? $data['movieID'] : null;
if (!$movieID) {
    $error = ["error" => "Missing required fields"];
    jsonResponse("Bad Request", 400, $error);
}

$query = "DELETE FROM `completedWatchList` WHERE `movieID` = ? AND `userID` = (SELECT `userID` FROM `users` WHERE `apiKey` = ?)";
$stmt = $pdo->prepare($query);
$stmt->execute([$movieID, $apiKey]);
$message = ["message" => "Delete movie successfully"];
jsonResponse("OK", 200, $message);




?>