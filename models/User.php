<?php
require_once __DIR__ . '/../core/core.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

   
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

 
    public function create($username, $password, $fullname = '', $role = 'user') {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, password, fullname, role)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$username, $hash, $fullname, $role]);
        return $this->pdo->lastInsertId();
    }

   
    public function createClientData($userId, $phone, $address, $number, $email) {
        $stmt = $this->pdo->prepare("
            INSERT INTO clients (user_id, phone, address_c, number_address, email, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $phone, $address, $number, $email]);
    }

   
    public function update($id, $data) {
        try {
            $this->pdo->beginTransaction();

     
            $userFields = [];
            $params = [];

            if (!empty($data['fullname'])) {
                $userFields[] = "fullname = ?";
                $params[] = $data['fullname'];
            }
            if (!empty($data['password'])) {
                $userFields[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            if (!empty($data['role'])) {
                $userFields[] = "role = ?";
                $params[] = $data['role'];
            }

            if ($userFields) {
                $params[] = $id;
                $sql = "UPDATE users SET " . implode(", ", $userFields) . " WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
            }

          
            $stmt = $this->pdo->prepare("
                UPDATE clients
                SET phone = ?, address_c = ?, number_address = ?, email = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $data['phone'] ?? '',
                $data['address_c'] ?? '',
                $data['number_address'] ?? '',
                $data['email'] ?? '',
                $id
            ]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Usuário atualizado com sucesso!'];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }

 
    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

   
    public function countByRole($role) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }
}
