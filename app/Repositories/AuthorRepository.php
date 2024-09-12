<?php

namespace App\Repositories;

use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;
use App\Models\Book;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AuthorRepository implements AuthorRepositoryInterface
{
    private function redisConnected(): bool
    {
        try {
            return Redis::ping() === '+PONG';
        } catch (Exception $e) {
            Log::error("Redis connection error: " . $e->getMessage());
            return false;
        }
    }

    public function index($filters, $perPage)
    {
        $query = Author::query();
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {
        // return Author::find($id);
        $author = Author::find($id);

        if ($this->redisConnected()) {
            $cachedAuthor = Redis::get("author_{$id}");

            if (isset($cachedAuthor)) {
                $author = json_decode($cachedAuthor, false);

                return $author;
            } else {
                Redis::set("author_$id", $author);

                return $author;
            }
        }

        return $author;
    }

    public function store(array $data)
    {

        $newAuthor = Author::create($data);
        if ($this->redisConnected()) {
            Redis::set("author_{$newAuthor->id}", $newAuthor);
        }

        return $newAuthor;
    }

    public function update(array $data, $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return null;
        }

        $updatedAuthor = Author::whereId($id)->update($data);
        if ($this->redisConnected()) {
            Redis::del("author_$id");
            Redis::set("author_{$updatedAuthor->id}", $updatedAuthor);
            return $updatedAuthor;
        }

        return $updatedAuthor;
    }

    public function delete($id)
    {

        Author::destroy($id);

        if ($this->redisConnected()) {
            Redis::del("author_$id");
        }
    }

    public function books($id)
    {
        $cachedAuthorBooks = Redis::get("author_{$id}_books");

        if (isset($cachedAuthorBooks)) {
            $authorBooks = json_decode($cachedAuthorBooks);

            return $authorBooks;
        } else {
            $authorBooks = Book::with('author')->where('author_id', $id)->get();
            Redis::set("author_{$id}_books", $authorBooks);
            return $authorBooks;
        }
    }
}
