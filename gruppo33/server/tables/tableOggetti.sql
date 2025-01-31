-- Tabella: oggetti
CREATE TABLE oggetti (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(20) UNIQUE NOT NULL,
    imgpath VARCHAR(255) NOT NULL,
    dettagli JSONB NOT NULL,
    score INT DEFAULT 0 NOT NULL
);
