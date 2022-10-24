<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Pedido;
use App\Models\Usuario;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class PedidoRepository extends AbstractRepository
{


    public function __construct()
    {

    }

    public function table(Request $request)
    {
        try {

            $pagina = $request->get("pagina", 1);
            $quantidade = $request->get("quantidade", 50);
            $ordem = $request->get("ordem", "numero");
            $sentido = $request->get("sentido", "asc");
            $procurar = $request->get('procurar');
            $filtros = $request->except('procurar', 'pagina', 'quantidade', 'ordem', 'sentido');


            switch ($ordem) {
                default:
                    $ordem = "pedido." . $ordem;
            }


            $query = Pedido::query()
                ->selectRaw("pedido.*, cliente.nome as cliente_nome")
                ->join("cliente", "cliente.id", "=", "pedido.cliente_id")
                ->orderBy($ordem, $sentido);


            if(key_exists('status', $filtros)){
                $query->where("pedido.status", "=", $filtros['status']);
            }

            if(key_exists('tipoPagamento', $filtros)){
                $query->where("pedido.tipoPagamento", "=", $filtros['tipoPagamento']);
            }

            if(!empty($procurar)){
                $query->where(function ($query) use ($procurar) {
                    $query->where("pedido.numero", "LIKE", "%".$procurar."%")
                    ->orWhere("cliente.nome", "LIKE", "%".$procurar."%");
                });
            }

            $pedidos = $query->paginate($quantidade, "*", '', $pagina);

            return response()->json([
                'menssage' => "",
                'retorno' => $pedidos,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }

    public function detalhe($id)
    {
        try {

            $pedido = Pedido::query()
                ->selectRaw("
                cliente.nome as cliente_nome,
                cliente.documento as cliente_documento,
                cliente.email as cliente_email,
                cliente.telefone as cliente_telefone,
                pedido.id,
                pedido.numero,
                pedido.status,
                pedido.tipoPagamento,
                pedido.tipoEntrega,
                pedido.valorTotal,
                pedido.tipoEntrega,
                pedido.created_at,
                'Crédito' as 'paragentoDescricao',
                'mastercard' as 'paragentoCodigo',
                1 as pagamentoParcelas,
                'Observação do Pedido' as observacao,
                20 as prazoEntrega,
                '2022-02-01' as dataPrevista,
                99.90 as valorProdutos,
                0 as valorDesconto,
                0 as valorFrete,
                '192.168.0.1' as compra_ip,
                'Rua Étore Mantovani' as enderecoLogradouro,
                '313' as enderecoNumero,
                'Centro' as enderecoBairro,
                'Socorro' as enderecoMunicipio,
                'SP' as enderecoUF,
                '09210-080' as enderecoCep")
                ->leftJoin("cliente", "cliente.id", "=", "pedido.cliente_id")
                ->where("pedido.id", "=", $id)
                ->first();

            if (!$pedido) {
                throw new \Exception("Pedido não encontrado");
            }

            return response()->json([
                'menssage' => "",
                'retorno' => $pedido,
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


    public function atualziarStatus($id)
    {
        try {

            sleep(2);
            $pedido = Pedido::query()
                ->where("pedido.id", "=", $id)
                ->first();

            if (!$pedido) {
                throw new \Exception("Pedido não encontrado");
            }

            if($pedido->status == 3){
                $pedido->status = 0;
            }else{
                $pedido->status = $pedido->status + 1;
            }

            if (!$pedido->save()) {
                return response()->json([
                    'menssage' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $pedido->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados gravados com sucesso!",
                'data' => ['id' => $pedido->id],
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


}
