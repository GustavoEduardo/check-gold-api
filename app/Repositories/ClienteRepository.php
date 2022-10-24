<?php

namespace App\Repositories;

use App\Helpers\MyHelpers;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Marca;

use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClienteRepository extends AbstractRepository
{


    public function __construct()
    {

    }

    public function table(Request $request)
    {
        try {

            $pagina = $request->get("pagina", 1);
            $quantidade = $request->get("quantidade", 50);
            $ordem = $request->get("ordenar", "nome");
            $sentido = $request->get("sentido", "asc");
            $procurar = $request->get('procurar');
            $filtros = $request->except('procurar', 'pagina', 'quantidade', 'ordem', 'sentido');


            switch ($ordem) {
                case 'grupo_nome':
                    $ordem = "grupoCliente.nome";
                    break;
                default:
                    $ordem = "cliente." . $ordem;
            }


            $query = Cliente::query()
                ->selectRaw("cliente.*, grupoCliente.nome as grupo_nome")
                ->leftJoin("grupoCliente", "grupoCliente.id", "=", "cliente.grupoCliente_id");
            $query->orderBy($ordem, $sentido);


            if (!empty($filtros['grupoCliente_id'])) {
                $query->where('grupoCliente_id', "=", $filtros['grupoCliente_id']);
            }

            if(key_exists('status', $filtros)){

                if ($filtros['status'] == "ativo"){
                    $query->where("cliente.ativo", "=", 1);
                }elseif($filtros['status'] == "inativo"){
                    $query->where(function ($query) {
                        $query->where("cliente.ativo", "=", 0)
                            ->orWhereNull("cliente.ativo");
                    });
                }
            }


            if(!empty($procurar)){
                $query->where(function ($query) use ($procurar) {
                    $query->where("cliente.nome", "LIKE", "%".$procurar."%");
                });
            }

            $clientes = $query->paginate($quantidade, "*", '', $pagina);


            foreach ($clientes as $cliente) {
                $cliente->cadastradoEm = MyHelpers::diferencaCadastro($cliente->created_at);
            }


            return response()->json([
                'menssage' => "",
                'retorno' => $clientes,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }


    public function detalhe($id)
    {
        try {


            $cliente = Cliente::query()
                ->find($id);

            if (!$cliente) {
                throw new \Exception("Cliente não encontrado");
            }

            return response()->json([
                'menssage' => "",
                'retorno' => $cliente,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }


    public function gravar($request, $id = null)
    {
        try {

            $dadosToken = new DecriptJwt;
            $usuarioId = $dadosToken->decriptJwt();


            $dados = $request->all();

            if (!empty($id)) {
                $cliente = Cliente::query()->find($id);

                if (!$cliente) {
                    throw new \Exception("Cliente não encontrado.", 422);
                }
            } else {
                $cliente = new Cliente();
                $cliente->created_from = $usuarioId;
            }

            $cliente->updated_from = $usuarioId;

            if (key_exists('nome', $dados)) {
                $cliente->nome = $dados['nome'];
            }

            if (key_exists('email', $dados)) {
                $cliente->email = $dados['email'];
            }

            if (key_exists('documento', $dados)) {
                $cliente->documento = MyHelpers::onlyNumeric($dados['documento']);

            }

            if (key_exists('grupoCliente_id', $dados)) {
                $cliente->grupoCliente_id = $dados['grupoCliente_id'];
            }

            if (key_exists('nascimentoData', $dados)) {
                $cliente->nascimentoData = $dados['nascimentoData'];
            }

            if (key_exists('telefone', $dados)) {
                $cliente->telefone = $dados['telefone'];
            }

            if (key_exists('observacoes', $dados)) {
                $cliente->observacoes = $dados['observacoes'];
            }


            if (key_exists('senha', $dados) && !empty($dados['senha'])) {
                $cliente->senha = Hash::make($dados['senha']);
            }

            if (key_exists('ativo', $dados)) {
                $cliente->ativo = $dados['ativo'];
            }

            if (key_exists('tipoPessoa', $dados)) {
                $cliente->tipoPessoa = $dados['tipoPessoa'];
            }


            if (!$cliente->save()) {

                $errors = [];

                foreach ($cliente->getErrors()->getMessages() as $messages)
                    foreach ($messages as $m)
                        $errors[] = $m;


                return response()->json([
                    'menssage' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => (array)$cliente->getErrors()->getMessages()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados gravados com sucesso!",
                'data' => ['id' => $cliente->id],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro ao gravar dados!",
                'data' => null,
                'errors' => (array)$e->getMessage()
            ], 500);
        }

    }


    public function remover($id = null)
    {
        try {


            $cliente = Cliente::query()->find($id);

            if (!$cliente) {
                throw new \Exception("Clinte não encontrado.", 422);
            }


            if (!$cliente->delete()) {
                return response()->json([
                    'menssage' => "Erro ao remover dados!",
                    'data' => null,
                    'errors' => $cliente->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados removidos com sucesso!",
                'data' => [],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro ao remover dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }


}
