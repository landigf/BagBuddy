<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once '../logindb.php';

    if (!isset($_SESSION['user'])) {
        echo json_encode(['error' => 'Utente non loggato']);
        exit;
    }

    $sortCriteria = $_POST['sortCriteria'] ?? 'created_at';
    $sortOrder = $_POST['sortOrder'] ?? 'DESC';

    /*
    $_SESSION['user'] = array(
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'info' => $user['info'],
        'preferences' => $user['preferences']
    );
    */
    $_SESSION['user']['preferences'] = [
        'sortCriteria' => $sortCriteria,
        'sortOrder' => $sortOrder
    ];

    $userID = $_SESSION['user']['id']; // userID dell'utente loggato per recuperare le valigie

    $conn = pg_connect($connection_string) or die(json_encode(['error' => 'Connessione al database fallita']));

    $query = "SELECT * FROM valigie WHERE id_utente = $userID ORDER BY $sortCriteria $sortOrder"; // ordinamento per preferenze

    $valigie = pg_query($conn, $query) or die(json_encode(['error' => 'Query fallita']));

    $bags = [];
    while ($row = pg_fetch_assoc($valigie)) {
        $bags[] = $row;
    }

    pg_close($conn);

    header('Content-Type: application/json');
    echo json_encode(['bags' => $bags]);
} else {
    echo json_encode(['error' => 'Richiesta non valida']);
}
?>