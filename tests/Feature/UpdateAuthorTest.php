<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateAuthorTest extends TestCase
{
    /**
     * It can fails on updating author due to validation errors
     */
    /** @test */
    public function it_fails_to_update_an_author_due_to_validation_errors()
    {
        $data = [
            'bio' => 'This author has no name.',
            'birth_date' => '1990-03-15'
        ];

        $author = Author::factory()->create();

        $response = $this->putJson("/api/v1/authors/{$author->id}", $data);

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
     * Update fail if author with certain id not found
     */
    /** @test */
    public function update_fail_if_author_not_found()
    {
        $book = Author::factory()->create();

        $response = $this->delete("/api/v1/authors/123");

        $response->assertStatus(404)->assertJson([
            'data' => null,
            'message' => "Author not found",
            'success' => true
        ]);
    }

    /**
     * Update author with given author id
     */
    /** @test */
    public function it_can_update_author_with_given_author_id()
    {

        $author = Author::factory()->create();

        $data = [
            'name' => 'Danang Ari Murti Wibowo',
            'bio' => 'This author has no bio',
            'birth_date' => '1990-03-15'
        ];

        $response = $this->putJson("/api/v1/authors/{$author->id}", $data);

        $response->assertStatus(201);

        $updatedAuthor = Author::find($author->id);

        $response->assertJson([
            'success' => true,
            'message' => 'Author successfully updated',
            'data' => [
                'id' => $updatedAuthor->id,
                'name' => $updatedAuthor->name,
                'bio' => $updatedAuthor->bio,
                'birth_date' => $updatedAuthor->birth_date,
            ]
        ]);
    }
}
