<?php
require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

// Take user's input
$newPriority = isset($_GET['priority']) ? $_GET['priority'] : null;
if (!$newPriority) {
    $error = ["error" => "Missing required fields"];
    jsonResponse("Bad Request", 400, $error);
}

// Query to update priority
$query = "UPDATE `toWatchList` SET `priority` = ? WHERE `movieID` = ? AND `userID` = (SELECT `userID` FROM `users` WHERE `apiKey` = ?)";
$stmt = $pdo->prepare($query);
$stmt->execute([$newPriority, $movieID, $apiKey]);
$message = ["message" => "Priority updated successfully"];
jsonResponse("OK", 200, $message);


?>