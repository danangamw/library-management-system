<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadBookTest extends TestCase
{
    /**
     * Fetch all book list successfully
     */
    /** @test */
    public function it_can_fetch_a_list_of_books()
    {
        Book::factory()->count(5)->create();

        $response = $this->get('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'publish_date',
                        'author'
                    ]
                ],
                'message'
            ]);
    }

    /**
     * Fetch a single book with given id
     */
    /** @test */
    public function it_can_fetch_a_single_book()
    {
        $book = Book::factory()->create();

        $response = $this->get("/api/v1/books/{$book->id}");

        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'publish_date' => $book->publish_date,
                'author' => [
                    'name' => $book->author->name
                ]
            ],
            'message' => "Book retrieved",
            'success' => true
        ]);
    }

    /**
     * return 404 not found error if given book id not found
     */
    /** @test */
    public function it_return_404_if_book_is_not_found()
    {


        $response = $this->getJson("/api/v1/books/1231123123");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => "Book not found",
            'data' => null,
            'success' => 'true'
        ]);
    }
}
