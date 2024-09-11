<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateAuthorTest extends TestCase
{
    /**
     * create an author successfully
     */
    /** @test */
    public function it_can_create_an_author_successfully()
    {

        $data = [
            'name' => 'Jane Doe',
            'bio' => 'Jane Doe is a writer and novelist.',
            'birth_date' => '1980-05-12'
        ];

        $response = $this->postJson('/api/v1/authors', $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'bio',
                'birth_date'
            ],
            'message'
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => 'Jane Doe',
            'bio' => 'Jane Doe is a writer and novelist.',
            'birth_date' => '1980-05-12'
        ]);
    }

    /**
     * It can fails on creating author due to validation errors
     */
    /** @test */
    public function it_fails_to_create_an_author_due_to_validation_errors()
    {
        $data = [
            'bio' => 'This author has no name.',
            'birth_date' => '1990-03-15'
        ];

        $response = $this->postJson('/api/v1/authors', $data);

        $response->assertStatus(422);

        $response->assertJson([
            'success' => false,
            'message' => 'Validation errors',
            'data' => [
                'name' => ['The name field is required.']
            ]
        ]);
    }
}
