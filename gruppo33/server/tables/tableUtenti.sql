-- Table: utenti
CREATE TABLE utenti (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, -- email unica
    domanda_sicurezza VARCHAR(255),
    risposta_sicurezza VARCHAR(255),
    info JSONB, -- per ora contiene nome, cognome
	preferences JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

