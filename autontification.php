<?php

class Auth {
    private $db;
    
    public function __construct(Database $database) {
        $this->db = $database->connect();
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM client WHERE email = ?";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = ($user['roleId'] == 1) ? 'admin' : 'client';

                    return ($user['roleId'] == 1) ? 'admin' : 'client';
                } else {
                    return "Invalid email or password.";
                }
            } else {
                return "No account found with this email.";
            }
        }
        return "An error occurred. Please try again later.";
    }
}
?>
