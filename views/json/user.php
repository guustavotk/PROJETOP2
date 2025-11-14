<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_ROOT . '/controllers/UserController.php';
require_once APP_ROOT . '/controllers/AuthController.php';

$action = $_GET['action'] ?? '';

$authController = new AuthController();
$userController = new UserController();

switch ($action) {

    // LOGIN
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) $input = $_POST;

            $username = trim($input['username'] ?? '');
            $password = trim($input['password'] ?? '');

            if ($username && $password) {
                $result = $authController->login($username, $password);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Preencha usuário e senha.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método inválido.']);
        }
        break;


    //  REGISTRO DE Usuario
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) $input = $_POST;

            // Dados recebidos do formulário
            $username        = trim($input['username'] ?? '');
            $password        = trim($input['password'] ?? '');
            $fullname        = trim($input['name'] ?? '');
            $phone           = trim($input['phone'] ?? '');
            $address_c       = trim($input['address_c'] ?? '');
            $number_address  = trim($input['number_address'] ?? '');
            $email           = trim($input['email'] ?? '');
            $role            = 'client';

            if ($username && $password && $fullname) {

                $result = $authController->register(
                    $username,
                    $password,
                    [
                        'fullname'        => $fullname,
                        'phone'           => $phone,
                        'address_c'       => $address_c,
                        'number_address'  => $number_address,
                        'email'           => $email,
                        'role'            => $role
                    ]
                );

                echo json_encode($result);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Preencha usuário, senha e nome completo.'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método inválido.']);
        }
        break;


    // Atualizar Usuario
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) $input = $_POST;

            $id             = (int)($input['id'] ?? 0);
            $fullname       = trim($input['fullname'] ?? $input['name'] ?? '');
            $address_c      = trim($input['address_c'] ?? '');
            $phone          = trim($input['phone'] ?? '');
            $password       = trim($input['password'] ?? '');
            $role           = trim($input['role'] ?? '');
            $number_address = trim($input['number_address'] ?? '');
            $email          = trim($input['email'] ?? '');

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de usuário inválido.']);
                exit;
            }

            $result = $userController->updateUser($id, [
                'fullname'        => $fullname,
                'address_c'       => $address_c,
                'phone'           => $phone,
                'password'        => $password,
                'role'            => $role,
                'number_address'  => $number_address,
                'email'           => $email
            ]);

            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Método inválido.']);
        }
        break;


    // Deslogar
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Sessão encerrada com sucesso.']);
        break;


    // Acao Invalida
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ação inválida. Use ?action=login, ?action=register ou ?action=update'
        ]);
}
