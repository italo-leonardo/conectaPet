<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index()
    {
        // Handle route requests.
    }

    public function store(){
        $cliente_model = new Cliente();
        $cliente = $this->getRequestBody();
        $cliente_model->save($cliente);
        return $this->response(200, ['success' => 'Cliente criado com sucesso!']);
    }
}
