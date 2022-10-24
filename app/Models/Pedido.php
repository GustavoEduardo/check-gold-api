<?php

namespace App\Models;


use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pedido extends ModelAbstract

{
    use Uuid;
    use HasApiTokens;
    use Notifiable;

    protected $table = 'pedido';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'nome'];

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
            'nome' => 'required|string|max:60',
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
            'nome.max:60' => trans("")
        ];

        return $messages;
    }


}
