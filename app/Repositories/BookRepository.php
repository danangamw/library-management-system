<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Support\Facades\Redis;

class BookRepository implements BookRepositoryInterface
{
    public function index(array $filters, $perPage = 25)
    {
        // return Book::with('author')->paginate(25);
        // Filter by search query if provided
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
        $cachedBook = Redis::get("book_{$id}");

        if (isset($cachedBook)) {
            $book = json_decode($cachedBook, false);

            return $book;
        } else {
            $book = Book::with('author')->find($id);
            Redis::set("book_$id", $book);

            return $book;
        }
    }

    public function store(array $data)
    {
        $newBook = Book::create($data);
        Redis::set("book_{$newBook->id}", $newBook);
        return $newBook;
    }

    public function update(array $data, $id)
    {
        Redis::del("book_$id");
        $book = Book::find($id);
        if ($book) {
            $book->update($data);
            $updatedBook = Book::find($id);
            Redis::set("book_$id", $updatedBook);
            return $book;
        }
        return null;
    }

    public function delete($id)
    {
        Book::destroy($id);
        Redis::del("book_$id");
    }
}
