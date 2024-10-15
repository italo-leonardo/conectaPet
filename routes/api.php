<?php

use Core\Router;
use Core\Response;

/* ~~~ Application Routes 🚦 ~~~  */

Router::get('/cadastro', 'AdminController::cadastro');
Router::get('/login', 'AdminController::login');

Router::post('/cliente', 'ClienteController::store');

Router::get('/pets', 'PetController::index');
Router::get('/pets/{id}', 'PetController::show');
Router::post('/pets', 'PetController::store');
Router::put('/pets/{id}', 'PetController::update');
Router::delete('/pets/{id}', 'PetController::destroy');