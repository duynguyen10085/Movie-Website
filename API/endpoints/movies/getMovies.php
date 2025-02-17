<?php
require_once '../includes/library.php';
$pdo = connectDB();
$action = isset($_GET['action']) ? $_GET['action'] : '';
$userID = isset($_GET['userID']) ? (int)$_GET['userID'] : null;

if ($movieID) {
    if($action === 'add_to_watchlist') {
        $query = "INSERT INTO toWatchList (movieID, userID) VALUES (?,?)";
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute([$movieID, $userID]);
        if ($success) {
            jsonResponse("OK", 201, ["message" => "Movie added to watchlist"]);
        } else {
            $error = ["error" => 'Failed to add movie to watchlist'];
            jsonResponse('Internal Server Error', 500, $error);
        }
    }

    else if ($lastendpoint === 'rating') {
        // GET /movies/{id}/rating
        $query = "SELECT vote_average FROM movies WHERE movieID = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$movieID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            jsonResponse("OK", 200, $result);

        } else {
            $error = ["error" => 'Movie not found'];
            jsonResponse('Not Found', 404, $error);
        }
    } else {
        // GET /movies/{id}
        $query = "SELECT * FROM movies WHERE movieID = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$movieID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            jsonResponse("OK", 200, $result);
        } else {
            $error = ["error" => 'Movie not found'];
            jsonResponse('Not Found', 404, $error);
        }
    }
} else {
    // GET /movies/
    $input = $_GET; // Using $_GET instead of file_get_contents('php://input')
    $title = isset($input['title']) ? trim($input['title']) : '';
    $page = isset($input['page']) ? (int)$input['page'] : 1;
    $limit = 40; // Number of movies per page
    $offset = ($page - 1) * $limit;
    
    if (isset($_GET['title'])) {
        $query = "SELECT * FROM movies WHERE title LIKE ? LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['%' . $title . '%', $limit, $offset]);
    } else {
        $query = "SELECT * FROM movies LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$limit, $offset]);
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse("OK", 200, $result);
}
?>