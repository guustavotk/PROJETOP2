<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }


    public function findByUsername($username) {
        return $this->userModel->findByUsername($username);
    }


    public function create($username, $password, $extra = []) {
        try {
            $fullname = $extra['fullname'] ?? ($extra['name'] ?? '');
            $phone    = $extra['phone'] ?? '';
            $address  = $extra['address_c'] ?? '';
            $number   = $extra['number_address'] ?? '';
            $email    = $extra['email'] ?? '';

      
            $userId = $this->userModel->create($username, $password, $fullname, 'user');

          
            $this->userModel->createClientData($userId, $phone, $address, $number, $email);

            return [
                'success' => true,
                'message' => 'Usuário cadastrado com sucesso!',
                'user_id' => $userId
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
        }
    }

 
public function updateUser($id, $data) {
    $update = $this->userModel->update($id, [
        'fullname'        => $data['fullname'] ?? $data['name'] ?? '',
        'password'        => $data['password'] ?? '',
        'role'            => $data['role'] ?? '',
        'phone'           => $data['phone'] ?? '',
        'address_c'       => $data['address_c'] ?? '',
        'number_address'  => $data['number_address'] ?? '',
        'email'           => $data['email'] ?? ''
    ]);

    return $update;
}

 
    public function countByRole($role) {
        return $this->userModel->countByRole($role);
    }

    public function countAll() {
        return $this->userModel->countAll();
    }
}
