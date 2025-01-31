<?php
session_start();
require_once '../logindb.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: ../../index.php");
    exit;
}

if(!isset($_SESSION['user'])){
    header("Location: ../../pages/login.php");
    exit;
}

if(!isset($_POST['oldPassword'])){
    echo json_encode(['error' => 'Old password not provided']);
    exit;
}

$oldPassword = $_POST['oldPassword'];
$userID = $_SESSION['user']['id'];

$conn = pg_connect($connection_string) or die('Connessione al database fallita');

$query = "SELECT password FROM utenti WHERE id = $1";
$result = pg_query_params($conn, $query, array($userID));

if ($row = pg_fetch_assoc($result)) {
    if (password_verify($oldPassword, $row['password'])) {
        echo json_encode(['correct' => true]);
    } else {
        echo json_encode(['correct' => false]);
    }
} else {
    echo json_encode(['error' => 'User not found']);
    header("Location: ../../pages/login.php");
}

pg_close($conn);
exit;

?>