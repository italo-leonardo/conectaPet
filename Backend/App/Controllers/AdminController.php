<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Admin;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AdminController extends Controller
{
    protected $admin_model;
    private $secret_key = 'sua_chave_secreta';

    public function __construct()
    {
        $this->admin_model = new Admin();
    }

    // Guarda Admin No Banco De Dados
    private function store($novo_admin)
    {
        $novo_admin['senha'] = password_hash($novo_admin['senha'], PASSWORD_BCRYPT);

        $this->admin_model->save($novo_admin);

        return $this->response(201, ['success' => 'Novo administrador criado']);
    }

    // Cadastra Novo Admin
    public function cadastrar($data)
    {
        $permission = $this->checkPermissions();

        if (!$permission) {
            return $this->response(401, ['error' => 'Permissão negada: apenas administradores de nível 2 podem cadastrar novos administradores']);
        }

        $data = $this->getRequestBody();

        if ($this->admin_model->findBy('email', $data['email'])) {
            return $this->response(409, ['error' => 'E-mail já cadastrado']);
        }

        return $this->store($data);
    }

    // Verifica Permissões
    public function checkPermissions()
    {
        $authData = $this->checkAuth();

        if (!$authData) {
            return false;
        }

        return $this->admin_model->isLevelTwoAdmin($authData->admin_id);
    }

    // Fazer Login De Admin
    public function login()
    {
        $body = $this->getRequestBody();
        $admin = $this->admin_model->findBy('email', $body['email']);

        if (!$admin || !$this->admin_model->verifyPassword($body['senha'], $admin['senha'])) {
            return $this->response(401, ['error' => 'Credenciais inválidas']);
        }

        $token = $this->generateJWT($admin);

        return $this->response(200, ['token' => $token]);
    }

    // Gera Token JWT
    private function generateJWT($admin)
    {
        $payload = [
            'iss' => "seu-dominio.com", // Emissor do token
            'aud' => "seu-dominio.com", // Público do token
            'iat' => time(), // Tempo em que o token foi emitido
            'nbf' => time(), // Tempo antes do qual o token não é válido
            'exp' => time() + (60 * 60), // Tempo de expiração do token (1 hora)
            'data' => [
                'admin_id' => $admin['id'],
                'email' => $admin['email'],
                'nivel' => $admin['nivel']
            ]
        ];

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    // Verifica Se Admin Tem Autorização
    public function checkAuth()
    {
        $token = $this->getBearerToken();

        if (!$token) {
            return $this->response(401, ['error' => 'Token não fornecido']);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return $this->response(401, ['error' => 'Token inválido']);
        }
    }

    // Pega os dados de cabeçalho
    private function getBearerToken()
    {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            return null;
        }

        return str_replace('Bearer ', '', $headers['Authorization']);
    }
}
