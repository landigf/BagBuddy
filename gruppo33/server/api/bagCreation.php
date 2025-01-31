<?php
$pathToIndex = ''; // Path per tornare alla pagina principale

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se non è stato inviato un form, reindirizza alla pagina principale
    $path = $pathToIndex . 'index.php?error=invalid_request';
    header('Location: ' . $path);
    exit();
}

session_start();
require_once '../logindb.php'; // Include $connection_string
require_once 'functions/bagCreationFunctions.php'; // Include ottieniPreferenze, calcolaConsigliati, getWeatherData

// Recupero i dati dal form id="valigia-form" in index.html
// Recupero i dati inviati dal form
$preferenze = ottieniPreferenze($_POST);

// Controllo se l'utente ha inviato lo stesso form due volte in meno di 60 secondi
if (isset($_SESSION['last_preferences']) && isset($_SESSION['last_submission_time'])) {
    $last_submission_time = $_SESSION['last_submission_time'];
    $current_time = time();
    $time_diff = $current_time - $last_submission_time;

    if ($preferenze === $_SESSION['last_preferences'] && $time_diff < 60) {
        // Se l'utente ha inviato lo stesso form due volte in meno di 10 secondi
        // non ricalcolo i consigliati
        if (isset($_SESSION['last_consigliati'])) {
            $consigliati = $_SESSION['last_consigliati'];
            $weatherData = $_SESSION['last_weatherData'];
        }
    }
}

$_SESSION['last_submission_time'] = time();
$_SESSION['last_preferences'] = $preferenze;

// Connessione al database
$conn = pg_connect($connection_string);

// Controllo connessione
if (!$conn) {
    $path = $pathToIndex . 'index.php?error=db_connection_error';
    header('Location: ' . $path);
    exit();
}

if (!isset($consigliati)) {

    // Recupero gli oggetti dal database
    $oggetti = pg_query($conn, $select_query);

    if (!$oggetti) {
        $path = $pathToIndex . 'index.php?error=select_query_error';
        header('Location: ' . $path);
        exit();
    }

    // Calcolo lo score degli oggetti
    $weatherData = getWeatherData($preferenze['destinazione'], $preferenze['departurDate'], $preferenze['returnDate']);
    $consigliati = calcolaConsigliati($oggetti, $preferenze, $weatherData);
    // verifica se calcolaConsigliati ha restituito un array o errore
    if (!is_array($consigliati)) {
        $path = $pathToIndex . 'index.php?error=calcolaConsigliati_error';
        header('Location: ' . $path);
        exit();
    }
    $_SESSION['last_consigliati'] = $consigliati;
    $_SESSION['last_weatherData'] = $weatherData;
}
?>

<?php ob_start(); ?>
<link rel="stylesheet" href="<?php echo $pathToIndex;?>src/css/bagCreation.css">
    <section id="bag">
        <h1 id="h1">Abbiamo generato la tua valigia!</h1>
        <div class="listContainer">
            <section id="recapContainer">
                <div class="recap" id="recap">
                    <h4>Hai selezionato: </h4>
                    <ul>
                    <?php
                    $destinazioneSimple = explode(',', $preferenze['destinazione'])[0];
                    $destinazioneSimple = trim($destinazioneSimple);
                    echo "<li id='destinazioneRecap'>". htmlspecialchars($destinazioneSimple) . "</li>";
                    echo "<li id='dataRecap'>" . htmlspecialchars($preferenze['departurDate']) . " - " . htmlspecialchars($preferenze['returnDate']) . "</li>";
                    echo "<li>Dimensione valigia: <span id='dimensioneRecap'>" . htmlspecialchars($preferenze['dimensione']) . "</span></li>";
                    echo "<li>Compagnia: <span id='compagniaRecap'>" . htmlspecialchars($preferenze['compagnia']) . "</span></li>";
                    echo "<li>Tipo di Viaggio: <span id='tipoDiViaggioRecap'>" . htmlspecialchars($preferenze['tipoDiViaggio']) . "</span></li>";
                    $attivita = [];
                    foreach ($preferenze as $key => $value) {
                        if ($value === true) {
                            $attivita[] = $key;
                        }
                    }
                    if (!empty($attivita)) {
                        echo "<li>Attività: <span id='attivitaRecap'>";
                        echo implode(", ", $attivita);
                    }
                    echo "</span></li>";
                    if(isset($weatherData) && is_array($weatherData)) {
                        $weather = [];
                        foreach ($weatherData as $key => $value) {
                            if ($value === true) {
                                $weather[] = $key;
                            }
                        }
                        if (!empty($weather)) {
                            echo "<li id='weather'>Condizioni Meteo Previste: ";
                            echo '<div class="weatherIcons">';
                            foreach ($weather as $condition) {
                                echo '<div class="weatherIcon">';
                                echo '<img src="' . $pathToIndex . 'images/bagCreation/weatherIcons/' . $condition . '.png" alt="' . $condition . '">';
                                echo '</div>';
                            }
                            echo '</div>';

                        }
                        echo "</li>";
                    }
                    ?>
                    </ul>
                </div>
                </section>
        
            <section id="bagListContainer">
                <div id="dropZone" ondrop="onDrop(event)" ondragover="onDragOver(event)">
                    <div class="bagList">
                        <label for="bagName">Inserisci il nome della tua valigia:</label>
                        <input type="text" id="bagName" placeholder="<?php echo htmlspecialchars($destinazioneSimple)?>" required/>
                        <ul id="list">
                            <?php
                            // Inseriamo i primi 3 consigliati nella valigia
                            for ($i = 0; $i < 3; $i++) {
                                echo '<li class="list tempItems">';
                                echo '<p>' . htmlspecialchars($consigliati[$i]['nome']) . '</p>';
                                echo '<div class="listButtons">';
                                echo '<button class="acceptButtons" onclick="acceptItem(this)">Accetta</button>';
                                echo '<button class="refuseButtons" onclick="refuseItem(this)">Rifiuta</button>';
                                echo '</div>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                        <div class="add">
                            <label for="newItem">Aggiungi un elemento alla valigia: </label><br>
                            <div class="addFlex">   
                                <input type="text" id="newItem" oninput="onTextInput(event)" onkeydown="onEnterPress(event)"/>
                                <button id="addButton" disabled="true" onclick="addTextItem()">Aggiungi</button>
                            </div>
                        </div>
                        <div class="save">
                            <button id="saveButton" disabled="true" onclick="onSaveBag()">Salva Valigia</button>
                        </div>
                    </div>
                </div>
                </section>
        </div>

        <?php
            echo '<div class="slider">';
            $imgPath = $pathToIndex . "images/oggetti/";
            $numeroVisualizzati = 15;
            for ($i = 0; $i < $numeroVisualizzati; $i++) {
                echo '<div class="item" draggable="true" onclick="onSliderItemClick(this)" ondragstart="onDragStart(event)">';
                $path = $imgPath . htmlspecialchars($consigliati[$i]['imgpath']);
                echo '<img draggable="false" src="' . $path . '" alt="' . htmlspecialchars($consigliati[$i]['nome']) . '">';
                echo '<p>' . htmlspecialchars($consigliati[$i]['nome']) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        ?>
    </section>
<?php $html = ob_get_clean(); ?>


<?php
    // Chiusura della connessione
    pg_close($conn);

    // Risposta AJAX
    header('Content-Type: text/html; charset=utf-8'); // Specifica il tipo di contenuto
    echo $html;
    exit();
?>


