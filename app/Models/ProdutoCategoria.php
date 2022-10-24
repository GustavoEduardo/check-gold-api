<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class ProdutoCategoria extends ModelAbstract

{
    use Uuid;
    use HasApiTokens;
    use Notifiable;

    protected $table = 'produtoCategoria';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'produto_id', 'categoria_id'];

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
            'produto_id' => 'required|string|max:36',
            'categoria_id' => 'required|string|max:36'
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
            'produto_id.required' => trans("Campo Produto Obrigatório"),
            'categoria_id.required' => trans("Campo Categoria Obrigatório")
        ];

        return $messages;
    }


}
