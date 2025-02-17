<?php
require '../includes/library.php';
require './checkApiKey/checkApiKey.php';
$pdo = connectDB();

if($lastendpoint === 'rating') {
    parse_str(file_get_contents('php://input'), $data);
    $newRating = isset($_GET['rating']) ? $_GET['rating'] : null;
    if ($newRating === null) {
        $error = ["error" => "Missing new rating"];
        jsonResponse("Bad Request", 400, $error);
    }

    $query = "SELECT `rating`, `movieID` FROM `completedWatchList` WHERE `movieID` = ?";
    $stmt = $pdo -> prepare($query);
    $stmt -> execute([$movieID]);
    $entry = $stmt -> fetch();

    if (!$entry) {
        $error = ["error" => "Entry not found"];
        jsonResponse("Not Found", 404, $error);
    }

    $movieID = $entry['movieID'];
    $oldRating = $entry['rating'];

    $query = "SELECT `vote_average`, `vote_count` FROM `movies` WHERE `movieID` = ?";
    $stmt = $pdo -> prepare($query);
    $stmt -> execute([$movieID]);
    $movie = $stmt -> fetch();

    $oldCount = $movie['vote_count'];
    $oldAvgRating = $movie['vote_average'];

    $query = "UPDATE `completedWatchList` SET `rating` = ? WHERE `movieID` = ?" ;
    $stmt = $pdo -> prepare($query);
    $stmt -> execute([$newRating, $movieID]);

    $updatedRating = (($oldAvgRating * $oldCount) - $oldRating + $newRating) / $oldCount;
    $query = "UPDATE `movies` SET `vote_average` = ? WHERE `movieID` = ?";
    $stmt = $pdo -> prepare($query);
    $stmt -> execute([$updatedRating, $movieID]);

    $message = ["message" => "Rating updated successfully"];
    jsonResponse("OK", 200, $message);

} else if($lastendpoint === 'times-watched') {
    $query = "SELECT `timesWatched` FROM `completedWatchList` WHERE `movieID` = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$movieID]);
    $entry = $stmt->fetch();

    if (!$entry) {
        $error = ["error" => "Entry not found"];
        jsonResponse("Not Found", 404, $error);
    }

    $timesWatched = $entry['timesWatched'] + 1;

    // Update the timesWatched and dateLastWatched in the completedWatchList
    $query = "UPDATE `completedWatchList` SET `timesWatched` = ?, `dateLastWatched` = NOW() WHERE `movieID` = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$timesWatched, $movieID]);

    $message = ["message" => "Times watched updated successfully"];
    jsonResponse("OK", 200, $message);


}



?>