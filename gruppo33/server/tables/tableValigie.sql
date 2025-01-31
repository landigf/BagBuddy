-- Table: valigie
CREATE TABLE valigie (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL, -- Nome identificativo della valigia
    id_utente INT NOT NULL, -- Collegamento all'utente proprietario
    preferenze JSONB NOT NULL, -- Contiene tutti i seguenti:
    -- destinazione VARCHAR(100) NOT NULL, -- Destinazione del viaggio
    -- departure_date DATE NOT NULL, -- Data di partenza
    -- return_date DATE, -- Data di ritorno (può essere NULL per viaggi di sola andata)
    -- dimensione VARCHAR(50) NOT NULL, -- Dimensione della valigia (es. grande, media, piccola)
    -- compagnia VARCHAR(100), -- Compagnia di trasporto (es. compagnia aerea)
    -- tipo_di_viaggio VARCHAR(100), -- Tipo di viaggio (es. lavoro, vacanza)
    -- attivita JSONB NOT NULL, -- Preferenze delle attività (es. { "spiaggia": true, "sport": false })
    lista_oggetti JSONB NOT NULL, -- Oggetti nella valigia (es. { "magliette": 5, "scarpe": 2 })
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data di creazione
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE
    -- Quando viene eliminato l'utene si eliminano tutte le sue valigie
);
