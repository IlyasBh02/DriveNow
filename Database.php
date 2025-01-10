<?php

class Database {
    private $host = "localhost";
    private $userName = "root";
    private $password = "";
    private $dbName = "Drivenow";
    public $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbName,
                $this->userName,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}


// try {
//     $sql = "SELECT * FROM reservation";
//     $stmt = $pdo->query($sql);
//     $reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     die("Erreur lors de la récupération des réservations : " . $e->getMessage());
// }
?>

