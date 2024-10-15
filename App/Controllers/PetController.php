<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Pet;

class PetController extends Controller
{
    protected $pet_model;

    public function __construct(){
        $this->pet_model = new Pet();
    }

    public function index()
    {
        $pets = $this->pet_model->all();
        return $this->response(200, $pets);
    }

    public function show($id){
        $pet = $this->pet_model->find($id);

        if(!$pet){
            $this->response(404, ['Message' => 'Pet não encontrado']);
        }

        $this->response(200, $pet);
    }

    public function store(){
        $this->pet_model->save($this->getRequestBody());
        return $this->response(201, ['sucess' => 'Novo pet criado!']);
    }

    public function update($id){
        $this->pet_model->update($id, $this->getRequestBody());
        return $this->response(200, ['sucess' => 'Alteração realizada com sucesso!']);
    }

    public function destroy($id){
        $pet = $this->pet_model->find($id);

        if(!$pet){
            return $this->response(404, ['error' => 'O pet não existe!']);
        }

        $this->pet_model->delete($id);
        return $this->response(200, ['sucess' => 'Pet excluído!']);
    }
}
