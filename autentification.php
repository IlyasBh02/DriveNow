<?php
session_start();
require "config.php";

class clientAuth{
    private $conn;
    public $nom;
    public $prenom;
    public $email;
    public $password;
    public $role = 'client';

    public function __construct()
    {
        $connection = new connection();
        $this->conn = $connection->getConnection();
    }

    public function registerClient($nom,$prenom,$email,$password){
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = password_hash($password,PASSWORD_BCRYPT);

        $sql = "insert into client(nom,prenom,email,password) values(:nom,:prenom,:email,:password)";
        $stmt = $this->conn->prepare($sql);
        

        $stmt->bindParam(":nom",$this->nom);
        $stmt->bindParam(":prenom",$this->prenom);
        $stmt->bindParam(":email",$this->email);
        $stmt->bindParam(":password",$this->password);

        if($stmt->execute()){
            $client_id = $this->conn->lastInsertId();

            $_SESSION['client_id'] = $client_id;
            $_SESSION['role'] = 'client';
            return true;
        }
        else{
            return false;
        }
}
public function loginClient($email,$password){
    $this->email = $email;

    $sql = "select * from client where email = :email";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":email",$this->email);

    $stmt->execute();

    if($stmt->rowCount() > 0){
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($password,$client['password'])){
            $_SESSION['client_id'] = $client['id'];
            $_SESSION['role'] = 'client';
            return true;
        }
        else{
            echo "invalid password";
        }

    }
    else{
        echo "this email not found";
    }
}

}