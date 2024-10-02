<?php

include_once 'paesi.php';

//creazione classe viaggio
class Viaggio {
    private $conn;
    private $table_name = "viaggi";

    public $paese_partenza;
    public $paese_arrivo;
    public $posti_disponibili;

    public function __construct($db) {
        $this->conn = $db;
    }

    //CRUD
    public function create() {
        $paese = new Paese($this->conn); 

        // Controlla se i paesi esistono
        if (!$paese->checkPaeseExists($this->paese_partenza) || !$paese->checkPaeseExists($this->paese_arrivo)) {
            return false; // Se uno dei paesi non esiste, restituisci false
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  SET paese_partenza = :paese_partenza, 
                      paese_arrivo = :paese_arrivo, 
                      posti_disponibili = :posti_disponibili";

        $stmt = $this->conn->prepare($query);
        $this->paese_partenza = htmlspecialchars(strip_tags($this->paese_partenza));
        $this->paese_arrivo = htmlspecialchars(strip_tags($this->paese_arrivo));
        $this->posti_disponibili = htmlspecialchars(strip_tags($this->posti_disponibili));

        $stmt->bindParam(":paese_partenza", $this->paese_partenza);
        $stmt->bindParam(":paese_arrivo", $this->paese_arrivo);
        $stmt->bindParam(":posti_disponibili", $this->posti_disponibili);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET paese_partenza = :paese_partenza, 
                      paese_arrivo = :paese_arrivo, 
                      posti_disponibili = :posti_disponibili 
                  WHERE paese_partenza = :paese_partenza 
                  AND paese_arrivo = :paese_arrivo";
    
        $stmt = $this->conn->prepare($query);
    
        // Imposta i valori
        $this->paese_partenza = htmlspecialchars(strip_tags($this->paese_partenza));
        $this->paese_arrivo = htmlspecialchars(strip_tags($this->paese_arrivo));
        $this->posti_disponibili = htmlspecialchars(strip_tags($this->posti_disponibili));
    
        // Bind dei parametri
        $stmt->bindParam(":paese_partenza", $this->paese_partenza);
        $stmt->bindParam(":paese_arrivo", $this->paese_arrivo);
        $stmt->bindParam(":posti_disponibili", $this->posti_disponibili);
    
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    

    public function read($paese_partenza = null, $paese_arrivo = null, $posti_disponibili = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
    
        // Aggiunge il filtro per paese_partenza se specificato
        if ($paese_partenza !== null) {
            $query .= " AND paese_partenza = :paese_partenza";
        }
    
        // Aggiunge il filtro per paese_arrivo se specificato
        if ($paese_arrivo !== null) {
            $query .= " AND paese_arrivo = :paese_arrivo";
        }
    
        // Aggiunge il filtro per posti_disponibili se specificato
        if ($posti_disponibili !== null) {
            $query .= " AND posti_disponibili = :posti_disponibili";
        }
    
        $stmt = $this->conn->prepare($query);
    
        // Bind dei parametri se i filtri sono stati forniti
        if ($paese_partenza !== null) {
            $stmt->bindParam(':paese_partenza', $paese_partenza);
        }
        if ($paese_arrivo !== null) {
            $stmt->bindParam(':paese_arrivo', $paese_arrivo);
        }
        if ($posti_disponibili !== null) {
            $stmt->bindParam(':posti_disponibili', $posti_disponibili);
        }
    
        $stmt->execute();
        return $stmt;
    }
    

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE paese_partenza = :paese_partenza AND paese_arrivo = :paese_arrivo";

        $stmt = $this->conn->prepare($query);
        $this->paese_partenza = htmlspecialchars(strip_tags($this->paese_partenza));
        $this->paese_arrivo = htmlspecialchars(strip_tags($this->paese_arrivo));

        $stmt->bindParam(":paese_partenza", $this->paese_partenza);
        $stmt->bindParam(":paese_arrivo", $this->paese_arrivo);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>





