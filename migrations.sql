-- Creazione della tabella Paesi
CREATE TABLE paesi (
    nome VARCHAR(255) PRIMARY KEY
);

-- Creazione della tabella Viaggi
CREATE TABLE viaggi (
    paese_partenza VARCHAR(255),
    paese_arrivo VARCHAR(255),
    posti_disponibili INT NOT NULL,
    FOREIGN KEY (paese_partenza) REFERENCES paesi(nome) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (paese_arrivo) REFERENCES paesi(nome) ON DELETE CASCADE ON UPDATE CASCADE
);

-- -- Inserimento di un nuovo Paese
-- INSERT INTO paesi (nome) VALUES ('Italia');

-- -- Inserimento di un nuovo Viaggio
-- INSERT INTO viaggi (paese_partenza, paese_arrivo, posti_disponibili) 
-- VALUES ('Italia', 'Spagna', 10);

-- -- Seleziona tutti i paesi
-- SELECT * FROM paesi;

-- -- Seleziona tutti i viaggi
-- SELECT * FROM viaggi;

-- -- Seleziona viaggi con filtro su paese_partenza
-- SELECT * FROM viaggi WHERE paese_partenza = 'Italia';

-- -- Seleziona viaggi con filtro su paese_arrivo e posti_disponibili
-- SELECT * FROM viaggi WHERE paese_arrivo = 'Spagna' AND posti_disponibili > 5;

-- -- Modifica di un viaggio
-- UPDATE viaggi
-- SET posti_disponibili = 8
-- WHERE paese_partenza = 'Italia' AND paese_arrivo = 'Spagna';

-- -- Modifica di un paese
-- UPDATE paesi
-- SET nome = 'Francia'
-- WHERE nome = 'Italia';

-- -- Eliminazione di un viaggio
-- DELETE FROM viaggi
-- WHERE paese_partenza = 'Francia' AND paese_arrivo = 'Spagna';

-- -- Eliminazione di un paese (questo elimina anche i viaggi collegati)
-- DELETE FROM paesi
-- WHERE nome = 'Francia';

