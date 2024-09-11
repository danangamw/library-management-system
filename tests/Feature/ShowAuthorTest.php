<?php

namespace Tests\Feature;

use App\Models\Author;
use Carbon\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowAuthorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * fetch a list of author successfully
     */
    /** @test */
    public function it_can_fetch_a_list_of_authors()
    {
        Author::factory()->count(5)->create();

        $response = $this->get('/api/v1/authors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'bio',
                        'birth_date'
                    ]
                ],
                'message'
            ]);
    }

    /**
     * fetch an author successfully
     */
    /** @test */
    public function it_can_fetch_a_single_authors()
    {
        $author = Author::factory()->create();

        $response = $this->get("/api/v1/authors/{$author->id}");

        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => $author->id,
                'name' => $author->name,
                'bio' => $author->bio,
                'birth_date' => $author->birth_date
            ],
            'message' => "Author retrieved",
            'success' => 'true'
        ]);
    }

    /**
     * return 404 not found error if given author id not found
     */
    /** @test */
    public function it_return_404_if_author_is_not_found()
    {


        $response = $this->getJson("/api/v1/authors/1231123123");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => "Author not found",
            'data' => null,
            'success' => 'true'
        ]);
    }
}
