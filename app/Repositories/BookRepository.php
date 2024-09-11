<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    public function index()
    {
        return Book::with('author')->get();
        // return Book::all();
    }

    public function getById($id)
    {
        return Book::with('author')->find($id);
    }

    public function store(array $data)
    {
        return Book::create($data);
    }

    public function update(array $data, $id)
    {
        $book = Book::find($id);
        if ($book) {
            $book->update($data);
            return $book;
        }
        return null;
    }

    public function delete($id)
    {
        Book::destroy($id);
    }
}
