<?php
require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

// Take input from user
parse_str(file_get_contents('php://input'), $data);
$userId = $pdo->query("SELECT `userID` FROM `users` WHERE `apiKey` = '{$apiKey}'") ->fetch()['userID'];
$movieId = isset($data['movieID']) ? $data['movieID'] : null;
$priority = isset($data['priority']) ? $data['priority'] : null;
$notes = isset($data['notes']) ? $data['notes'] :'';

// Check missing fields
if (!$priority || !$movieId) {
    $error = ["error" => "Missing required fields"];
    jsonResponse("Bad Request", 400, $error);
}

// Query to update existing entries
$query = "UPDATE `toWatchList` SET `priority` = ?, `notes` = ?, `movieID` = ? WHERE `userID` = ? AND `toWatchID` = ? ";
$stmt = $pdo->prepare($query);
$stmt->execute([$priority, $notes, $movieId, $userId, $toWatchID]);

if ($stmt->rowCount() > 0) {
    $message = ["message" => "Entry updated successfully"];
    jsonResponse("OK", 200, $message);
} else {
    // If no rows were updated, it means the entry did not exist, so we insert a new one.
    $query = "INSERT INTO `toWatchList` (`userID`, `movieID`, `priority`, `notes`) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId, $movieId, $priority, $notes]);
    $message = ["message" => "Entry created successfully"];
    jsonResponse("Created", 201, $message);
}
?>


?>
