<?php
require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

$userId = $pdo->query("SELECT `userID` FROM `users` WHERE `apiKey` = '{$apiKey}'") ->fetch()['userID'];
$movieId = isset($_GET['movieID']) ? $_GET['movieID'] : null;
$rating = isset($_GET['rating']) ? $_GET['rating'] : null;
$notes = isset($_GET['notes']) ? $_GET['notes'] :'';

$query = "SELECT `vote_average`, `vote_count` FROM `movies` WHERE `movieID` = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    $error = ["error" => "Movie not found"];
    jsonResponse("Not Found", 404, $error);
}

$currentAvgRating = $movie['vote_average'];
$currentCount = $movie['vote_count'];

// Insert a new entry
$query = "INSERT INTO `completedWatchList` (`userID`, `movieID`, `rating`, `notes`, `dateInitiallyWatched`, `dateLastWatched`, `timesWatched`) VALUES (?, ?, ?, ?, NOW(), NOW(), 1)";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId, $movieId, $rating, $notes]);

// Compute new average rating
$newCount = $currentCount + 1;
$newAvgRating = (($currentAvgRating * $currentCount) + $rating) / $newCount;

// Update the movie's rating and vote count
$query = "UPDATE `movies` SET `vote_average` = ?, `vote_count` = ? WHERE `movieID` = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$newAvgRating, $newCount, $movieId]);

$message = ["message" => "Entry added successfully"];
jsonResponse("Created", 201, $message);
?>
