<?php

//Creazione classe Paese
class Paese {
    private $conn;
    private $table_name = "paesi"; //riferimento tab.database

    public $nome;

    public function __construct($db) {
        $this->conn = $db;
    }

//CRUD
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nome = :nome";

        $stmt = $this->conn->prepare($query);
        $this->nome = htmlspecialchars(strip_tags($this->nome));

        $stmt->bindParam(":nome", $this->nome);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function checkPaeseExists($nome) {
        $query = "SELECT nome FROM " . $this->table_name . " WHERE nome = :nome LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $nome = htmlspecialchars(strip_tags($nome));
        $stmt->bindParam(":nome", $nome);
        $stmt->execute();
        return $stmt->rowCount() > 0; // Restituisce true se il paese esiste
    }

    public function read() {
        $query = "SELECT nome FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE nome = :nome";

        $stmt = $this->conn->prepare($query);
        $this->nome = htmlspecialchars(strip_tags($this->nome));

        $stmt->bindParam(":nome", $this->nome);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nome = :nome WHERE nome = :old_nome";

        $stmt = $this->conn->prepare($query);
        $this->nome = htmlspecialchars(strip_tags($this->nome));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":old_nome", $this->old_nome);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>

