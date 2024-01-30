<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Usuário pode ou não fazer a ação
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Regras para os campos de usuário no cadastro
        $rules = [
            "name"=> "required|min:3|max:255",
            "email"=> [
                "required",
                "email",
                "min:3",
                "max:255",
                "unique:users",
            ],
            "password"=> [
                "required",
                "min:6",
                "max:100",
            ],
        ];

        // Caso seja uma atualização de usuário, o e-mail único tem uma exceção para alteração do próprio e-mail e a senha não é obrigatória
        if ($this->method() === "PATCH") {
            $rules['email'] = [
                "required",
                "email",
                "min:3",
                "max:255",
                Rule::unique("users")->ignore($this->user),
            ];
            $rules['password'] = [
                "nullable",
                "min:6",
                "max:100",
            ];
        }

        return $rules;
    }
}
