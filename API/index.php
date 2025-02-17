<?php
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri);
define('__BASE__', '/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api');
$endpoint = str_replace(__BASE__, "", $uri["path"]);
$method = $_SERVER['REQUEST_METHOD'];

// Parse the URL and HTTP method
$routes = explode('/', trim($endpoint, '/'));
$resource = $routes[0] ?? null;

function handleMovies($method, $endpoint, $routes) {
    switch ($method) {
        case 'GET':
            if($endpoint === '/movies/' || preg_match('/^\/movies\/([1-9]\d*)$/', $endpoint, $matches) || preg_match('/^\/movies\/([1-9]\d*)\/rating$/', $endpoint, $matches)) {
                $movieID = isset($matches[1]) ? $matches[1] : null;
                $lastendpoint = isset($routes[2]) ? $routes[2] : null;
                require './endpoints/movies/getMovies.php';
            }
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'OPTIONS':
            jsonResponse("OK", 200);
            break;
        default:
        jsonResponse("Method Not Allowed", 405);
    }
}

function handleToWatchList($method, $endpoint) {
    switch ($method) {
        case 'GET':
            if($endpoint === '/towatchlist/entries') {
                require './endpoints/towatchlist/getToWatchList.php';
            } 
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'POST':
            if($endpoint === '/towatchlist/entries') {
                require './endpoints/towatchlist/insertToWatchList.php';
            } 
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'PUT':
            if(preg_match('/^\/towatchlist\/entries\/([1-9]\d*)$/', $endpoint, $matches)) {
                $toWatchID = $matches[1];
                require './endpoints/towatchlist/updateToWatchList.php';
            } 
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'PATCH':
            if (preg_match('/^\/towatchlist\/entries\/([1-9]\d*)\/priority$/', $endpoint, $matches)) {
                $movieID = $matches[1];
                require './endpoints/towatchlist/updatePriority.php';
            } 
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'DELETE':
            if(preg_match('/^\/towatchlist\/entries\/([1-9]\d*)$/', $endpoint, $matches)) {
                $movieID= $matches[1];
                require './endpoints/towatchlist/deleteToWatchList.php';
            } 
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'OPTIONS':
            jsonResponse("OK", 200);
            break;
        default:
            jsonResponse("Method Not Allowed", 405);
    }
}

function handleCompletedWatchList($method, $endpoint, $routes) {
    switch ($method) {
        case 'GET':
            if( $endpoint === '/completedwatchlist/entries' 
                 || preg_match('/^\/completedwatchlist\/entries\/([1-9]\d*)\/times-watched$/', $endpoint, $matches) 
                 || preg_match('/^\/completedwatchlist\/entries\/([1-9]\d*)\/rating$/', $endpoint, $matches)) {
                $movieID = isset($matches[1]) ? $matches[1] : null;
                $lastendpoint = isset($routes[3]) ? $routes[3] : null;
                require './endpoints/completedwatchlist/getCompletedWatchList.php';
            }
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'POST':
            if($endpoint === '/completedwatchlist/entries') {
                require './endpoints/completedwatchlist/insertCompletedWatchList.php';
            } 
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'PATCH':
            if(preg_match('/^\/completedwatchlist\/entries\/([1-9]\d*)\/rating$/', $endpoint, $matches) || 
            preg_match('/^\/completedwatchlist\/entries\/([1-9]\d*)\/times-watched$/', $endpoint, $matches)) {
                $movieID = isset($matches[1]) ? $matches[1] : null;
                $lastendpoint = isset($routes[3]) ? $routes[3] : null;
                require './endpoints/completedwatchlist/updateCompletedWatchedList.php';
            }
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'DELETE':
            if(preg_match('/^\/completedwatchlist\/entries\/([1-9]\d*)$/', $endpoint, $matches)) {
                $movieID = isset($matches[1]) ? $matches[1] : null;
                require './endpoints/completedwatchlist/deleteCompletedWatchList.php';
            }
            else {
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        case 'OPTIONS':
            jsonResponse("OK", 200);
            break;
        default:
            jsonResponse("Method Not Allowed", 405);
    }
}

function handleUsers($method, $endpoint) {
    switch ($method) {
        case 'GET':
            if(preg_match('/^\/users\/([1-9]\d*)\/stats/', $endpoint, $matches)) {
                $_GET['userID'] = $matches[1];
                require './endpoints/users/userStats.php';
            }
            else {       
                jsonResponse("Invalid endpoint", 400);
            }
            break;
        default:
        jsonResponse("Method Not Allowed", 405);
    }
}

// Route to appropriate handler function
switch ($resource) {
    case 'movies':
        handleMovies($method, $endpoint, $routes);
        break;
    case 'towatchlist':
        handleToWatchList($method, $endpoint);
        break;
    case 'completedwatchlist':
        handleCompletedWatchList($method, $endpoint, $routes);
        break;
    case 'users':
        handleUsers($method, $endpoint);
        break;
    default:
        jsonResponse("Not Found", 404);
}


function jsonResponse ($output, $id, $result = null) {
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-API-KEY, X_API_KEY, Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("HTTP/1.1 $id $output");
    if ($result !== null) {
        echo json_encode($result);
        exit();
    }
}
?>
