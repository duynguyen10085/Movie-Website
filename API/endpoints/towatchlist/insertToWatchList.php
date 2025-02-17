<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

$userId = $pdo->query("SELECT `userID` FROM `users` WHERE `apiKey` = '{$apiKey}'")->fetch()['userID'];
$movieId = isset($_GET['movieID']) ? $_GET['movieID'] : null;

if (!$movieId) {
    $error = ["error" => "Missing required fields"];
    jsonResponse("Bad Request", 400, $error);
    exit();
}

$checkQuery = "SELECT COUNT(*) FROM `toWatchList` WHERE `userID` = ? AND `movieID` = ?";
$stmt = $pdo->prepare($checkQuery);
$stmt->execute([$userId, $movieId]);
$exists = $stmt->fetchColumn();

if ($exists) {
    $error = ["error" => "Movie already in the watchlist"];
    jsonResponse("Conflict", 409, $error);
} else {
    $query = "INSERT INTO `toWatchList` (`userID`, `movieID`) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId, $movieId]);
    $message = ["message" => "Entry created successfully"];
    jsonResponse("Created", 201, $message);

}
?>
