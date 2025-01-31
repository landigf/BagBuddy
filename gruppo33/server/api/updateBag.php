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

// Recupero e decodifica del body della richiesta
$bag = json_decode(file_get_contents('php://input'), true);
if (!$bag) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}

// Controllo se l'utente è loggato
if (!isset($_SESSION['user'])) {
    $_SESSION['unsavedBag'] = $bag;
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato.']);
    exit;
}
$userId = $_SESSION['user']['id'];

$bagID = intval($bag['id']);
$bagName = pg_escape_string($conn, $bag['nome']);
$prefereze = pg_escape_string($conn, json_encode($bag['preferenze']));
$items = pg_escape_string($conn, json_encode($bag['items']));

// Verifico che la valigia esista e che l'utente sia il proprietario
$query = "SELECT * FROM valigie WHERE id = $1 AND id_utente = $2";
$result = pg_prepare($conn, "check_user", $query);
$result = pg_execute($conn, "check_user", array($bagID, $userId));
if (pg_num_rows($result) != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non hai i permessi per modificare questa valigia.']);
    exit;
}

$query = "
    UPDATE valigie
    SET nome = '$bagName', preferenze = '$prefereze', lista_oggetti = '$items'
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