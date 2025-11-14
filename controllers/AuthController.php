<?php
require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/../core/core.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new UserController();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    
    public function login($username, $password) {
        if (!$username || !$password) {
            return ['success' => false, 'message' => 'Preencha usuário e senha.'];
        }

        $user = $this->user->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Usuário ou senha inválidos.'];
        }

        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'name'     => $user['fullname'],
            'role'     => $user['role']
        ];

        $redirect = ($user['role'] === 'admin') ? 'painel.php' : 'index.php';

        return [
            'success'  => true,
            'message'  => 'Login realizado com sucesso!',
            'redirect' => $redirect
        ];
    }

    
    public function register($username, $password, $extra = []) {
        if (!$username || !$password) {
            return ['success' => false, 'message' => 'Preencha usuário e senha.'];
        }

        if ($this->user->findByUsername($username)) {
            return ['success' => false, 'message' => 'Usuário já existe.'];
        }

    
        $result = $this->user->create($username, $password, $extra);

        if (!$result['success']) {
            return $result;
        }

        $_SESSION['user'] = [
            'id'       => $result['user_id'],
            'username' => $username,
            'name'     => $extra['fullname'] ?? ($extra['name'] ?? ''),
            'role'     => 'user'
        ];

        return [
            'success'  => true,
            'message'  => 'Usuário cadastrado com sucesso!',
            'redirect' => 'index.php'
        ];
    }


    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Sessão encerrada com sucesso.'];
    }
}
