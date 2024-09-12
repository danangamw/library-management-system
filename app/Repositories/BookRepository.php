<?php

namespace App\Repositories;

use App\Classes\ApiResponseClass;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BookRepository implements BookRepositoryInterface
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

    public function index(array $filters, $perPage = 25)
    {
        $query = Book::with('author');

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhereHas('author', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
        }

        return $query->paginate($perPage);
    }

    public function getById($id)
    {

        // return Book::with('author')->find($id);
        $book = Book::with('author')->find($id);

        if ($this->redisConnected()) {
            return Book::with('author')->find($id);
            $cachedBook = Redis::get("book_{$id}");

            if (isset($cachedBook)) {
                $book = json_decode($cachedBook, false);
                return $book;
            } else {
                Redis::set("book_$id", $book);
                return $book;
            }
        }

        return $book;
    }

    public function store(array $data)
    {
        if ($this->redisConnected()) {
            $newBook = Book::create($data);
            Redis::set("book_{$newBook->id}", $newBook);
            return $newBook;
        }

        $newBook = Book::create($data);
        return $newBook;
    }

    public function update(array $data, $id)
    {
        $updatedBook = Book::whereId($id)->update($data);
        if (!$updatedBook) {
            return null;
        }

        if ($this->redisConnected()) {
            Redis::del("book_$id");
            $book = Book::find($id);

            Redis::set("book_$id", $updatedBook);
            return $updatedBook;
        }

        return $updatedBook;
    }

    public function delete($id)
    {
        Book::destroy($id);
        if ($this->redisConnected()) {
            Redis::del("book_$id");
        }
    }
}
