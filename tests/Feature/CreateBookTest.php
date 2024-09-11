<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateBookTest extends TestCase
{
    /**
     * create an book successfully
     */
    /** @test */
    public function it_can_create_an_book_successfully()
    {
        $author = Author::factory()->create();

        $data = [
            'title' => 'Lord of the rings',
            'description' => 'A book about a ring',
            'publish_date' => '1980-05-12',
            'author_id' => $author->id
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'title',
                'description',
                'publish_date',
                'author' => [
                    'name'
                ]
            ],
            'message'
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Lord of the rings',
            'description' => 'A book about a ring',
            'publish_date' => '1980-05-12',
            'author_id' => $author->id
        ]);
    }

    /**
     * It can fails on create book due to validation errors
     */
    /** @test */
    public function it_fails_to_create_an_book_due_to_validation_errors()
    {
        $data = [
            'description' => 'This has no title.',
            'publish_date' => '1990-03-15'
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);

        $response->assertJson([
            'success' => false,
            'message' => 'Validation errors',
            'data' => [
                'title' => ['The title field is required.'],
                'author_id' => ["The author id field is required."]
            ]
        ]);
    }
}
