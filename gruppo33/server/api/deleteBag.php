<?php
session_start();

require_once '../logindb.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito.']);
    exit;
}


$conn = pg_connect($connection_string);
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}

// Controllo se l'utente è loggato
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato.']);
    exit;
}
$userID = $_SESSION['user']['id'];

$bagID = intval($data['id']);

// Verifico che la valigia esista e che l'utente sia il proprietario
$query = "SELECT * FROM valigie WHERE id = $1 AND id_utente = $2";
$result = pg_prepare($conn, "check_user", $query);
$result = pg_execute($conn, "check_user", array($bagID, $userID));
if (pg_num_rows($result) != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non hai i permessi per modificare questa valigia.']);
    exit;
}

$query = "
    DELETE FROM valigie
    WHERE id = $bagID
";

$result = pg_query($conn, $query);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Valigia salvata con successo.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio: ' . pg_last_error($conn)]);
}

pg_close($conn);
?>