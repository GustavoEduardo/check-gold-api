<?php

namespace App\Models;


use App\Helpers\DecriptJwt;
use App\Jobs\SumarizarObrigacaoEmpresa;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use LaravelLegends\PtBrValidator\Rules\Cpf;
use LaravelLegends\PtBrValidator\Rules\CpfOuCnpj;


class Cliente extends ModelAbstract

{
    use Uuid;


    protected $table = 'cliente';

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
    protected $fillable = ['id', 'nome', 'email', 'documento', 'grupoCliente_id', 'nascimentoData', 'telefone', 'observacoes', 'ativo', 'tipoPessoa',
        'ultimoLoginData', 'created_at', 'created_from', 'updated_at', 'updated_from'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['senha'];

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
    protected $attributes = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $id = $this->getKey();

        $rules = [
            'nome' => 'required|string|max:60',
            'documento' => ['unique:cliente,documento', 'required', new CpfOuCnpj],
            'email' => ['unique:cliente,email', 'required'],

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
            'nome.max:60' => trans(""),
            'email.unique' => trans('E-mail já em uso por outro cliente'),
            'documento.unique' => trans('Documento já em uso por outro cliente'),
            'documento.required' => trans('Campo documento obrigatório'),
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


}
