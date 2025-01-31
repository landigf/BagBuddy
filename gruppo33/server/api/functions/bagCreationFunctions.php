<?php
$select_query = "SELECT * FROM oggetti";

function ottieniPreferenze($form) {
    $risposte = [
        'destinazione' => trim($form['destinazione']),
        'departurDate' => $form['departurDate'],
        'returnDate' => $form['returnDate'],
        'dimensione' => $form['dimensione'],
        'compagnia' => $form['compagnia'],
        'tipoDiViaggio' => $form['tipoDiViaggio'],
        'spiaggia' => false,
        'montagna' => false,
        'escursionismo' => false,
        'sport' => false,
        'relax' => false,
        'campeggio' => false
    ];
    if(isset($form['attivita']) && !empty($form['attivita'])){
        foreach ($form['attivita'] as $attivita) {
            switch ($attivita) {
                case 'spiaggia':
                    $risposte['spiaggia'] = true;
                    break;
                case 'montagna':
                    $risposte['montagna'] = true;
                    break;
                case 'escursionismo':
                    $risposte['escursionismo'] = true;
                    break;
                case 'sport':
                    $risposte['sport'] = true;
                    break;
                case 'relax':
                    $risposte['relax'] = true;
                    break;
                case 'campeggio':
                    $risposte['campeggio'] = true;
                    break;
            }
    }
    }
    return $risposte;
}

function calcolaConsigliati($oggetti, $preferenze, $weatherData) {
    $sortedByScore = [];
    // weather data
    // $weatherData = [
    //     'isCold' => true,
    //     'isHot' => false,
    //     'isRainy' => false,
    //     'isWindy' => false,
    //     'isSnowy' => false,
    //     'isSunny' => false,
    //     'isCloudy' => false
    // ];
    while ($row = pg_fetch_assoc($oggetti)) {
        $score = $row['score'];
        // Calcola lo score in base alle preferenze
        $dettagli = json_decode($row['dettagli'], true);
        if (is_array($weatherData)) {
            foreach ($weatherData as $key => $value) {
                if ($value)
                    $score += $dettagli['destinazione'][$key];
            }
        }
        
        $score += $dettagli['dimensione'][$preferenze['dimensione']];
        $score += $dettagli['compagnia'][$preferenze['compagnia']];
        $score += $dettagli['tipoDiViaggio'][$preferenze['tipoDiViaggio']];
        $score += $dettagli['spiaggia'][$preferenze['spiaggia'] ? 'si' : 'no'];
        $score += $dettagli['montagna'][$preferenze['montagna'] ? 'si' : 'no'];
        $score += $dettagli['escursionismo'][$preferenze['escursionismo'] ? 'si' : 'no'];
        $score += $dettagli['sport'][$preferenze['sport'] ? 'si' : 'no'];
        $score += $dettagli['relax'][$preferenze['relax'] ? 'si' : 'no'];
        $score += $dettagli['campeggio'][$preferenze['campeggio'] ? 'si' : 'no'];

        $sortedByScore[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'imgpath' => $row['imgpath'],
            'score' => $score
        ];
    }

    usort($sortedByScore, function($a, $b) {
        return $b['score'] - $a['score'];
    });

    return $sortedByScore;
}

function getWeatherData($destinazione, $departureDate, $returnDate) {
    $apiKey = '99497e02fdb0b41bdb8ca9a17fdd6bae';
    $destinazione = str_replace(' ', '', trim($destinazione));
    $url = "http://api.openweathermap.org/data/2.5/forecast?q={$destinazione}&appid={$apiKey}&units=metric";

    $response = @file_get_contents($url);
    if ($response === false) {
        error_log("Errore: impossibile connettersi all'API di OpenWeather.");
        return "Errore: impossibile connettersi all'API di OpenWeather.";
    }

    $data = json_decode($response, true);

    // Verifica se la risposta contiene errori
    if (!isset($data['cod']) || $data['cod'] !== "200") {
        $message = $data['message'] ?? 'Errore sconosciuto';
        error_log("Errore: {$message}");
        return "Errore: {$message}";
    }

    // Calcolo delle temperature e condizioni
    $temperatures = [];
    $conditions = [];

    foreach ($data['list'] as $entry) {
        $date = new DateTime($entry['dt_txt']);
        if ($date->format('Y-m-d') >= $departureDate && $date->format('Y-m-d') <= $returnDate) {
            $temperatures[] = $entry['main']['temp'];
            $conditions[] = $entry['weather'][0]['main']; // Condizioni meteo
        }
    }

    // Verifica se ci sono dati per il periodo indicato
    if (empty($temperatures) || empty($conditions)) {
        return "Errore: nessun dato disponibile per il periodo selezionato.";
    }

    // Calcola la temperatura media
    $averageTemp = array_sum($temperatures) / count($temperatures);

    // Determina le condizioni meteo
    $isCold = $averageTemp < 15; // Freddo sotto i 15°C
    $isHot = $averageTemp > 25; // Caldo sopra i 25°C
    $isRainy = in_array('Rain', $conditions); // Previsione di pioggia
    $isWindy = in_array('Wind', $conditions); // Previsione di vento
    $isSnowy = in_array('Snow', $conditions); // Previsione di neve
    $isSunny = in_array('Clear', $conditions); // Previsione di sole
    $isCloudy = in_array('Clouds', $conditions); // Previsione di nuvoloso

    // Restituisce i booleani per ogni condizione
    return [
        'isCold' => $isCold,
        'isHot' => $isHot,
        'isRainy' => $isRainy,
        'isWindy' => $isWindy,
        'isSnowy' => $isSnowy,
        'isSunny' => $isSunny,
        'isCloudy' => $isCloudy
    ];
}
?>