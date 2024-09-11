<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateBookTest extends TestCase
{
    /**
     * It can fails on update book due to validation errors
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

    /**
     * Update fail if book with certain id not found
     */
    /** @test */
    public function update_fail_if_book_not_found()
    {
        $book = Book::factory()->create();

        $response = $this->delete("/api/v1/books/123");

        $response->assertStatus(404)->assertJson([
            'data' => null,
            'message' => "Book not found",
            'success' => true
        ]);
    }

    /**
     * Update author with given book id
     */
    /** @test */
    public function it_can_update_book_with_given_author_id()
    {

        $book = Book::factory()->create();
        $author = Author::factory()->create();

        $data = [
            'title' => 'Game of Throne',
            'description' => 'This book has no description',
            'publish_date' => '1990-03-15',
            'author_id' => $author->id
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200);

        $updatedBook = Book::find($book->id);

        $response->assertJson([
            'success' => true,
            'message' => 'Book successfully updated',
            'data' => [
                'title' => 'Game of Throne',
                'description' => 'This book has no description',
                'publish_date' => '1990-03-15',
                'author' => [
                    'name' => $author->name
                ]
            ]
        ]);
    }
}
