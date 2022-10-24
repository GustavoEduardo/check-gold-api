<?php

namespace App\Models;


use App\Helpers\DecriptJwt;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use LaravelLegends\PtBrValidator\Rules\CpfOuCnpj;


class GrupoCliente extends ModelAbstract

{
    use Uuid;


    protected $table = 'grupoCliente';

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
    protected $fillable = ['id', 'nome', 'created_at', 'created_from', 'updated_at', 'updated_from'];

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
            'nome' => ['unique:grupoCliente,nome', 'required'],
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
            'nome.unique' => trans('Nome já em uso por outro Grupo'),
            'nome.required' => trans('Campo nome obrigatório'),
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
