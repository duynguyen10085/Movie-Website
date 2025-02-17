<?php
require '../includes/library.php';
$pdo = connectDB();

$userId = $_GET['userID'];

// Check if user exists
$query = "SELECT * FROM `users` WHERE `userID` = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $error = ["error" => "User not found"];
    jsonResponse("Not Found", 404, $error);
}

// Total Time Watched
$query = "SELECT SUM(movies.runtime * completedWatchList.timesWatched) AS Total_Watched_Time FROM completedWatchList
          JOIN movies ON completedWatchList.movieID = movies.movieID
          WHERE completedWatchList.userID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$totalTimeWatched = $stmt->fetch();

// Average Score
$query = "SELECT AVG(`rating`) as Average_Score
          FROM `completedWatchList`
          WHERE `userID` = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$averageScore = $stmt->fetch();

// Planned Time to Watch
$query = "SELECT SUM(movies.runtime) AS Total_Planned_Time FROM toWatchList
          JOIN movies ON toWatchList.movieID = movies.movieID
          WHERE toWatchList.userID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$totalMoviesWatched = $stmt->fetch();

// Total Movies Watched
$query = "SELECT COUNT(*) AS Total_Movies_Watched FROM completedWatchList
          WHERE userID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$plannedTimeToWatch = $stmt->fetch();

$stats = [
    $totalTimeWatched,
    $averageScore,
    $totalMoviesWatched,
    $plannedTimeToWatch
];

jsonResponse("OK", 200, $stats);
?>
