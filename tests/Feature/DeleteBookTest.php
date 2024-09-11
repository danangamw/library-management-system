<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteBookTest extends TestCase
{
    /**
     * Delete book with certain id
     */
    /** @test */
    public function it_can_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->delete("/api/v1/books/{$book->id}");

        $response->assertStatus(200)->assertJson([
            'data' => null,
            'message' => "Book deleted",
            'success' => true
        ]);
    }

    /**
     * Delete fail if book with certain id not found
     */
    /** @test */
    public function delete_fail_if_book_not_found()
    {
        $book = Book::factory()->create();

        $response = $this->delete("/api/v1/books/123");

        $response->assertStatus(404)->assertJson([
            'data' => null,
            'message' => "Book not found",
            'success' => true
        ]);
    }
}
