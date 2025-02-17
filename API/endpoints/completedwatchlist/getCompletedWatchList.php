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

if ($movieID) {
    if ($lastendpoint === 'rating') {
        $query = "SELECT `rating` FROM `completedWatchList` 
        WHERE `movieID` = ? AND `userID` = (SELECT `userID` FROM `users` WHERE `apiKey` = ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$movieID, $apiKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            jsonResponse("OK", 200, $result);
        }
    } else if($lastendpoint === 'times-watched') {
        $query = "SELECT `timesWatched` FROM `completedWatchList` WHERE `movieID` = ? AND `userID` = (SELECT `userID` FROM `users` WHERE `apiKey` = ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$movieID, $apiKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            jsonResponse("OK", 200, $result);
        } else {
            $error = ["error" => 'Movie not found'];
            jsonResponse('Not Found', 404, $error);
        }
    }
} else {
    parse_str(file_get_contents('php://input'), $data);
    $sort = isset($data['sort']) ? $data['sort'] : '';
    $movieId = isset($_GET['movieID']) ? $_GET['movieID'] : "";
    $title = isset($_GET['title']) ? trim($_GET['title']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 40; // Number of movies per page
    $offset = ($page - 1) * $limit;

    if (isset($_GET['title'])) {
        $query = "SELECT completedWatchList.*, movies.*
        FROM `completedWatchList`
        JOIN `movies` ON completedWatchList.movieID = movies.movieID
        WHERE completedWatchList.userID = (SELECT userID FROM users WHERE apiKey = ?) AND movies.title LIKE ? LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$apiKey, '%' . $title . '%', $limit, $offset]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse("OK", 200, $result); }
    else if ($sort === 'most-watched') {
        $query = "SELECT movies.*, completedWatchList.* 
                  FROM completedWatchList 
                  JOIN movies ON movies.movieID = completedWatchList.movieID 
                  WHERE userID = (SELECT userID FROM users WHERE apiKey = ?) 
                  ORDER BY timesWatched DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$apiKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse("OK", 200, $result);
    } else if ($sort === 'best-rated') {
        $query = "SELECT movies.*, completedWatchList.* 
                  FROM completedWatchList 
                  JOIN movies ON movies.movieID = completedWatchList.movieID 
                  WHERE userID = (SELECT userID FROM users WHERE apiKey = ?) 
                  ORDER BY rating DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$apiKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse("OK", 200, $result);
    } else {
        if (!empty($movieId)) {
            $query = "SELECT *
                      FROM completedWatchList 
                      WHERE movieID = ? 
                      AND userID = (SELECT userID FROM users WHERE apiKey = ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$movieId, $apiKey]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            jsonResponse("OK", 200, $result);
        } else {
            $query = "SELECT movies.*, completedWatchList.* 
                      FROM completedWatchList 
                      JOIN movies ON movies.movieID = completedWatchList.movieID 
                      WHERE userID = (SELECT userID FROM users WHERE apiKey = ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$apiKey]);
            $result = $stmt->fetchAll();
            if($result) {
                jsonResponse("OK", 200, $result);
            }
            
        }
    }
}

?>
