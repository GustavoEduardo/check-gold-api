<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FiltrosController;
use App\Http\Controllers\GrupoClienteController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioLogadoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes

Route::group(['namespace' => 'Api'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);

    });

    Route::group(['prefix' => 'public'], function () {

        Route::group(['prefix' => 'cadastros'], function () {
            Route::post('/usuario', [UsuarioController::class, 'gravar']);

        });

    });


});

//
Route::group(['middleware' => 'apiJwt',], function () {

// Usuário Logado
    Route::get('/usuario-logado', [UsuarioLogadoController::class, 'index']);

    Route::group(['prefix' => 'cadastros'], function () {

        Route::post('/usuario', [UsuarioController::class, 'gravar']);

        Route::get('/marcas', [MarcaController::class, 'table']);
        Route::post('/marca', [MarcaController::class, 'gravar']);
        Route::put('/marca/{id}', [MarcaController::class, 'gravar']);
        Route::delete('/marca/{id}', [MarcaController::class, 'remover']);

        Route::get('/categorias', [CategoriaController::class, 'table']);
        Route::post('/categoria', [CategoriaController::class, 'gravar']);
        Route::put('/categoria/{id}', [CategoriaController::class, 'gravar']);
        Route::delete('/categoria/{id}', [CategoriaController::class, 'remover']);

        Route::get('/clientes', [ClienteController::class, 'table']);
        Route::get('/cliente/{id}', [ClienteController::class, 'detalhe']);
        Route::post('/cliente', [ClienteController::class, 'gravar']);
        Route::put('/cliente/{id}', [ClienteController::class, 'gravar']);
        Route::delete('/cliente/{id}', [ClienteController::class, 'remover']);

        Route::get('/gruposClientes', [GrupoClienteController::class, 'table']);
        Route::post('/grupoCliente', [GrupoClienteController::class, 'gravar']);
        Route::put('/grupoCliente/{id}', [GrupoClienteController::class, 'gravar']);
        Route::delete('/grupoCliente/{id}', [GrupoClienteController::class, 'remover']);

        Route::get('/produtos', [ProdutoController::class, 'table']);
        Route::get('/produtos/gerarSku', [ProdutoController::class, 'gerarSku']);
        Route::get('/produto/{id}', [ProdutoController::class, 'detalhe']);
        Route::post('/produto', [ProdutoController::class, 'gravar']);
        Route::put('/produto/{id}', [ProdutoController::class, 'gravar']);
        Route::delete('/produto/{id}', [ProdutoController::class, 'remover']);

        Route::get('/pedidos', [PedidoController::class, 'table']);
        Route::get('/pedido/{id}', [PedidoController::class, 'detalhe']);
        Route::get('/pedido/{id}/atualziarStatus', [PedidoController::class, 'atualziarStatus']);


    });


    Route::group(['prefix' => 'filtros'], function () {

        Route::get('/marcas', [FiltrosController::class, 'marcas']);
        Route::get('/categorias', [FiltrosController::class, 'categorias']);
        Route::get('/gruposClientes', [FiltrosController::class, 'gruposClientes']);
        Route::get('/pagamentos', [FiltrosController::class, 'pagamentos']);


    });

    Route::group(['prefix' => 'administradores'], function () {

        Route::post('/usuario', [UsuarioController::class, 'gravar']);
        Route::get('/usuario', [UsuarioController::class, 'table']);
        Route::put('/usuario/{id}', [UsuarioController::class, 'editar']);
        Route::delete('/usuario/{id}', [UsuarioController::class, 'remover']);



    });


});
