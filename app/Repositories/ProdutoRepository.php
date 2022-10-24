<?php

namespace App\Repositories;

use App\Helpers\MyHelpers;
use App\Models\Cliente;
use App\Models\GrupoCliente;
use App\Helpers\DecriptJwt;
use App\Models\Produto;
use App\Models\ProdutoCategoria;
use Illuminate\Http\Request;


class ProdutoRepository extends AbstractRepository
{


    public function __construct()
    {

    }

    public function table(Request $request)
    {
        try {

            $pagina = $request->get("pagina", 1);
            $quantidade = $request->get("quantidade", 50);
            $ordem = $request->get("ordem", "nome");
            $sentido = $request->get("sentido", "asc");
            $procurar = $request->get('procurar');
            $filtros = $request->except('procurar', 'pagina', 'quantidade', 'ordem', 'sentido');



            $query = Produto::query();
            $query->orderBy($ordem, $sentido);

            if(key_exists('status', $filtros)){

                if ($filtros['status'] == "ativo"){
                    $query->where("ativo", "=", 1);
                }elseif($filtros['status'] == "inativo"){
                    $query->where(function ($query) {
                        $query->where("ativo", "=", 0)
                            ->orWhereNull("ativo");
                    });
                }
            }


            if(!empty($procurar)){
                $query->where(function ($query) use ($procurar) {
                    $query->where("nome", "LIKE", "%".$procurar."%");
                });
            }


            $produtos = $query->paginate($quantidade, "*", '', $pagina);

            return response()->json([
                'menssage' => "",
                'retorno' => $produtos,
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

    public function gerarSku(Request $request)
    {
        try {

            $existe = true;
            do{
                $skuCodigo = MyHelpers::genSku();

                $existe = !empty(Produto::query()->where('skuCodigo', "=", $skuCodigo)->first());

            }while($existe);




            return response()->json([
                'menssage' => "",
                'retorno' => $skuCodigo,
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


            $produto = Produto::query()
                ->find($id);

            if (!$produto) {
                throw new \Exception("Produto não encontrado");
            }

            $produto->categorias = $produto->getCategorias($produto->id);

            return response()->json([
                'menssage' => "",
                'retorno' => $produto,
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
            $usuarioLogadoId = $dadosToken->decriptJwt();


            $dados = $request->all();

            if (!empty($id)) {
                $produto = Produto::query()->find($id);

                if (!$produto) {
                    throw new \Exception("Produto não encontrado.", 422);
                }
            } else {
                $produto = new Produto();
                $produto->created_from = $usuarioLogadoId;
            }

            $produto->updated_from = $usuarioLogadoId;

            if (key_exists('nome', $dados)) {
                $produto->nome = $dados['nome'];
            }
            if (key_exists('tipo', $dados)) {
                $produto->tipo = $dados['tipo'];
            }
            if (key_exists('marca_id', $dados)) {
                $produto->marca_id = $dados['marca_id'];
            }
            if (key_exists('ncm', $dados)) {
                $produto->ncm = $dados['ncm'];
            }
            if (key_exists('freteTipo', $dados)) {
                $produto->freteTipo = $dados['freteTipo'];
            }
            if (key_exists('fretePreco', $dados)) {
                $produto->fretePreco = $dados['fretePreco'];
            }
            if (key_exists('skuDisponivel', $dados)) {
                $produto->skuDisponivel = $dados['skuDisponivel'];
            }
            if (key_exists('skuCodigo', $dados)) {
                $produto->skuCodigo = $dados['skuCodigo'];
            }
            if (key_exists('codigoBarras', $dados)) {
                $produto->codigoBarras = $dados['codigoBarras'];
            }
            if (key_exists('precoCusto', $dados)) {
                $produto->precoCusto = $dados['precoCusto'];
            }
            if (key_exists('precoVenda', $dados)) {
                $produto->precoVenda = $dados['precoVenda'];
            }
            if (key_exists('precoPromocional', $dados)) {
                $produto->precoPromocional = $dados['precoPromocional'];
            }
            if (key_exists('peso', $dados)) {
                $produto->peso = $dados['peso'];
            }
            if (key_exists('largura', $dados)) {
                $produto->largura = $dados['largura'];
            }
            if (key_exists('altura', $dados)) {
                $produto->altura = $dados['altura'];
            }
            if (key_exists('comprimento', $dados)) {
                $produto->comprimento = $dados['comprimento'];
            }
            if (key_exists('descricao', $dados)) {
                $produto->descricao = $dados['descricao'];
            }
            if (key_exists('garantia', $dados)) {
                $produto->garantia = $dados['garantia'];
            }
            if (key_exists('ativo', $dados)) {
                $produto->ativo = $dados['ativo'];
            }
            if (key_exists('comVariante', $dados)) {
                $produto->comVariante = $dados['comVariante'];
            }


            if (!$produto->save()) {
                return response()->json([
                    'menssage' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $produto->getErrors()
                ], 422);
            }


            if (key_exists('categorias', $dados) && is_array($dados['categorias'])) {

                ProdutoCategoria::query()
                    ->where("produto_id", "=", $produto->id)
                    ->delete();
                foreach ($dados['categorias'] as $categoria_id){
                    $newProdutoCategoria = new ProdutoCategoria();
                    $newProdutoCategoria->produto_id = $produto->id;
                    $newProdutoCategoria->categoria_id = $categoria_id;
                    $newProdutoCategoria->created_from = $usuarioLogadoId;
                    $newProdutoCategoria->updated_from = $usuarioLogadoId;
                    $newProdutoCategoria->save();
                }
            }

            return response()->json([
                'menssage' => "Dados gravados com sucesso!",
                'data' => ['id' => $produto->id],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro ao gravar dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }


    public function remover($id = null)
    {
        try {


            $produto = Produto::query()->find($id);

            if (!$produto) {
                throw new \Exception("Produto não encontrado.", 422);
            }


            if (!$produto->delete()) {
                return response()->json([
                    'menssage' => "Erro ao remover dados!",
                    'data' => null,
                    'errors' => $produto->getErrors()
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
