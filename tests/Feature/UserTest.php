<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Requisição GET para /users.
     */
    public function test_get(): void
    {
        $response = $this->get('/users');

        $response->assertStatus(200);
    }

    /**
     * Requisição POST para /users deve retornar status 201 e o objeto do usuário criado.
     */
    public function test_post(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/users', [
                    "name" => "Teste",
                    "email" => "teste@prodemge.gov.br",
                    "password" => "teste123"
                ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has("data.id")
                    ->where("data.name", "Teste")
                    ->where("data.email", "teste@prodemge.gov.br")
                    ->where("data.created", "2024-01-30")
                    ->missing('data.password')
            );
    }

    /**
     * Requisição GET para /users/teste@prodemge.gov.br deve retornar o objeto do usuário.
     */
    public function test_get_usuario_por_email(): void
    {
        $response = $this->get('/users/teste@prodemge.gov.br');

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has("data.id")
                    ->where("data.name", "Teste")
                    ->where("data.email", "teste@prodemge.gov.br")
                    ->where("data.created", "2024-01-30")
                    ->missing('data.password')
            );
    }

    /**
     * Requisição POST para /users deve retornar status 422 e uma mensagem avisando que já existe um usuário registrado com este e-mail.
     */
    public function test_post_com_email_repetido(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->postJson('/users', [
                    "name" => "Teste",
                    "email" => "teste@prodemge.gov.br",
                    "password" => "teste123"
                ]);

        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where("message", "The email has already been taken.")
                    ->where("errors.email", ["The email has already been taken."])
            );
    }

    /**
     * Requisição PATCH para /users/teste@prodemge.gov.br deve retornar status 200 e o objeto do usuário alterado.
     */
    public function test_patch(): void
    {
        $get = $this->get('/users/teste@prodemge.gov.br');
        $id = $get->decodeResponseJson()["data"]["id"];

        $url = strval("/users/" . $id);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->patchJson($url, [
                    "name" => "Teste Alterado",
                    "email" => "teste@prodemge.gov.br",
                    "password" => "123teste"
                ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has("data.id")
                    ->where("data.name", "Teste Alterado")
                    ->where("data.email", "teste@prodemge.gov.br")
                    ->where("data.created", "2024-01-30")
                    ->missing('data.password')
            );
    }

    /**
     * Requisição DELETE para /users/{id} deve retornar status 204 indicando que não exite mais o usuário com o e-mail teste@prodemge.gov.br.
     */
    public function test_delete(): void
    {
        $get = $this->get('/users/teste@prodemge.gov.br');
        $id = $get->decodeResponseJson()["data"]["id"];

        $url = strval("/users/" . $id);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->delete($url);

        $response->assertStatus(204);
    }
}
