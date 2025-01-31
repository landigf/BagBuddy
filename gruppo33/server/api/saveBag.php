<?php
session_start();
require_once '../logindb.php';


$conn = pg_connect($connection_string);
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database.']);
    exit;
}

// Controllo del metodo HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito.']);
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


if (!$bag) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}


/*
pg_escape_string() è una funzione che permette di evitare SQL injection
Parameters
connection
An PgSql\Connection instance. When connection is unspecified, the default connection is used. The default 
connection is the last connection made by pg_connect() or pg_pconnect().

Warning
As of PHP 8.1.0, using the default connection is deprecated.

data
A string containing text to be escaped.
*/

// Estrazione dei dati della valigia e prevenzione di SQL injection
$bagName = pg_escape_string($conn, $bag['nome']);
$prefereze = pg_escape_string($conn, json_encode($bag['preferenze']));
$items = pg_escape_string($conn, json_encode($bag['items']));

$query = "
    INSERT INTO valigie (nome, id_utente, preferenze, lista_oggetti)
    VALUES ('$bagName', $userId, '$prefereze', '$items')
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