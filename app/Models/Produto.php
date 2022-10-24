<?php

namespace App\Models;


use App\Helpers\DecriptJwt;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use LaravelLegends\PtBrValidator\Rules\CpfOuCnpj;


class Produto extends ModelAbstract

{
    use Uuid;


    protected $table = 'produto';

    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id',
        'nome',
        'tipo',
        'marca_id',
        'ncm',
        'freteTipo',
        'fretePreco',
        'skuDisponivel',
        'skuCodigo',
        'codigoBarras',
        'precoCusto',
        'precoVenda',
        'precoPromocional',
        'peso',
        'largura',
        'altura',
        'comprimento',
        'descricao',
        'garantia',
        'ativo',
        'comVariante',
        'created_at', 'created_from', 'updated_at', 'updated_from'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $attributes = ['ativo' => 1, 'freteTipo' => 'D'];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $id = $this->getKey();

        $rules = [
            'nome' => ['unique:produto,nome', 'required'],
            'skuCodigo' => ['unique:produto,skuCodigo', 'required'],
            'tipo' => ['required'],
        ];

        if ($id) {

            $rules = array_intersect_key($rules, $this->getDirty());

        }

        return $rules;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [
            'nome.unique' => trans('Nome de produto já em uso.'),
            'skuCodigo.unique' => trans('SKU de produto já em uso.'),
            'nome.required' => trans('Campo nome obrigatório'),
            'skuCodigo.required' => trans('Campo skuCodigo obrigatório'),
            'tipo.required' => trans('Campo tipo obrigatório'),
        ];

        return $messages;
    }

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();

    }

    /**
     * @return array
     */
    public static function getCategorias($produto_id)
    {

        return ProdutoCategoria::query()
            ->selectRaw("categoria.id, categoria.nome")
            ->where("produtoCategoria.produto_id", "=", $produto_id)
            ->join("categoria", "categoria.id", "=","produtoCategoria.categoria_id")
            ->orderBy("categoria.nome", "asc")
            ->get();

    }


}
