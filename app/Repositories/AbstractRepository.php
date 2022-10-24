<?php

namespace App\Repositories;

use App\Helpers\DecriptJwt;
use App\Models\ComplianceTributo;
use App\Models\Conta;
use App\Models\ContaInfraestrutura;
use App\Models\Logs;
use App\Tenant\ManagerTenant;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class AbstractRepository
{
    use ValidatesRequests;

    protected $model;
    protected $decript;

    public function __construct()
    {
        $this->model = $this->resolveModel();
        $this->decript = new DecriptJwt;
    }

    /**
     * Deleta um dado da tabela pelo ID.
     * @param object $model representa qual Model deve ser chamada quando ele consultar algo no banco.
     * @param int $id representa qual id o será buscado no banco antes de deletar.
     * @param string $nameFile verifica se a model tem algum arquivo a ser deletado.
     * @return [json] com mensagem de sucesso ou error
     */

    public function destroy($id)
    {
        if (isset($this->usuarioId) && $this->usuarioId == true) {
            $idConta = $this->decript->decriptJwt();
            $data = $this->model->where('conta_id', $idConta)->where('id', $id)->first();
        } else {
            $data = $this->model->find($id);
        }
        if ($data) {

            $log = new Logs();
            $log->tipo = 'Info';
            $log->user_id = Auth::user()->id;
            $log->user = Auth::user()->name;
            $log->acao = 'Deletou o dado ' . $data->id . ' da tabela ' . $this->nameTable;
            $log->data_acao = date("Y-m-d");
            $log->save();

            $data->delete();
            if (isset($this->tree) && $this->tree == true) {
                $data = $this->model->tree();
                return response()->json([
                    'success' => trans('messages.deleted'),
                    'tree' => $data
                ], 200);
            } else {
                return response()->json(['success' => trans('messages.deleted')], 200);
            }
        } else {
            return response()->json([
                'message' => trans('messages.given_data'),
                'errors' => ['id' => trans('messages.404')]
            ], 404);
        }
    }

    public function campos()
    {
        return response()->json($this->model::tabela());
    }

    /**
     * Resolve model
     * @return [string] model object
     */

    protected function resolveModel()
    {
        return app($this->model);
    }

    /**
     * Resolve model
     * @return [array] rules object
     */

    protected function resolveRequest()
    {
        return app($this->myRequest);
    }



}
