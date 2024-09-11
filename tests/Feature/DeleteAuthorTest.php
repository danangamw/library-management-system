<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteAuthorTest extends TestCase
{
    /**
     * Delete author with certain id
     */
    /** @test */
    public function it_can_delete_author()
    {
        $author = Author::factory()->create();

        $response = $this->delete("/api/v1/authors/{$author->id}");

        $response->assertStatus(200)->assertJson([
            'data' => null,
            'message' => "Author successfully deleted",
            'success' => true
        ]);
    }

    /**
     * Delete fail if author with certain id not found
     */
    /** @test */
    public function delete_fail_if_author_not_found()
    {
        $author = Author::factory()->create();

        $response = $this->delete("/api/v1/authors/123");

        $response->assertStatus(404)->assertJson([
            'data' => null,
            'message' => "Author not found",
            'success' => true
        ]);
    }
}
