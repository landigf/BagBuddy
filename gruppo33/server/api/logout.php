<?php
session_start();
if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: ../../index.php");
    exit;
}
require_once '../logindb.php';

$userID = $_SESSION['user']['id'];
$conn = pg_connect($connection_string) or die(json_encode(['error' => 'Connessione al database fallita']));
if (isset($_POST['removeUser']) && $_POST['removeUser'] === 'true') {
    // Rimuove l'utente dal database
    $query = "DELETE FROM utenti WHERE id = $userID";
    $result = pg_query($conn, $query) or die(json_encode(['error' => 'Query fallita']));
} else {
    // Aggiorna le preferenze dell'utente prima del logout
    $query = "UPDATE utenti SET preferences = $1 WHERE id = $2";
    $result = pg_query_params($conn, $query, [json_encode($_SESSION['user']['preferences']), $userID]) or die(json_encode(['error' => 'Query fallita']));
}
pg_close($conn);

// Distruggere la sessione
session_unset();
session_destroy();

$response = array("status" => "success", "message" => "Logged out successfully");
echo json_encode($response);
header("Location: ../../index.php");
exit;
?>