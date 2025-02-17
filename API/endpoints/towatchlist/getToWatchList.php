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
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$input = $_GET; // Using $_GET instead of file_get_contents('php://input')
$title = isset($_GET['title']) ? trim($_GET['title']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 40; // Number of movies per page
$offset = ($page - 1) * $limit;


if ($movieId) {
    // Check if the movie is already in the watchlist
    $checkQuery = "SELECT COUNT(*) FROM `toWatchList` WHERE `userID` = ? AND `movieID` = ?";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([$userId, $movieId]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $response = ["status" => "exists"];
        jsonResponse("OK", 200, $response);
    } else {
        $response = ["status" => "not exists"];
        jsonResponse("OK", 200, $response);
    }
} else {
    // Retrieve the list of movies in the watchlist
    if ($priority) {
        $query = "
            SELECT toWatchList.*, movies.*
            FROM `toWatchList`
            JOIN `movies` ON toWatchList.movieID = movies.movieID
            WHERE toWatchList.userID = ? 
            AND toWatchList.priority = ? 
            ORDER BY toWatchList.priority ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId, $priority]);
    } else {
        if (isset($_GET['title'])) {
            $query = "SELECT toWatchList.*, movies.*
            FROM `toWatchList`
            JOIN `movies` ON toWatchList.movieID = movies.movieID
            WHERE toWatchList.userID = ? AND movies.title LIKE ? LIMIT ? OFFSET ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$userId, '%' . $title . '%', $limit, $offset]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse("OK", 200, $result);
        }else {
            $query = "
            SELECT toWatchList.*, movies.*
            FROM `toWatchList`
            JOIN `movies` ON toWatchList.movieID = movies.movieID
            WHERE toWatchList.userID = ? 
            ORDER BY toWatchList.priority ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                jsonResponse("OK", 200, $result);
            } else {
                jsonResponse("No entries found", 404);
            }
        }
    }
        
}

?>
