<?php
function ottieniValigieDiUtente($conn, $idUtente) {
    $query = "SELECT * FROM valigia WHERE id_utente = $idUtente";
    $result = pg_query($conn, $query);
    return $result;
}

function ottieniValigia($conn, $idValigia) {
    $query = "SELECT * FROM valigia WHERE id = $idValigia";
    $result = pg_query($conn, $query);
    return $result;
}

function ottieniTutteLeValigie($conn) {
    $query = "SELECT * FROM valigia";
    $result = pg_query($conn, $query);
    return $result;
}
?>