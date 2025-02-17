<?php
require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

if (!$movieID) {
    $error = ["error" => "Missing required fields"];
    jsonResponse("Bad Request", 400, $error);
}

// Delete from toWatchList with movieID
$query = "DELETE FROM `toWatchList` WHERE `movieID` = ? AND `userID` = (SELECT `userID` FROM `users` WHERE `apiKey` = ?)";
$stmt = $pdo->prepare($query);
$stmt->execute([$movieID, $apiKey]);
$message = ["message" => "Delete movie successfully"];
jsonResponse("OK", 200, $message);


?>