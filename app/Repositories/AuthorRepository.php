<?php

namespace App\Repositories;

use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;
use App\Models\Book;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function index()
    {
        return Author::all();
    }

    public function getById($id)
    {
        return Author::find($id);
    }

    public function store(array $data)
    {
        return Author::create($data);
    }

    public function update(array $data, $id)
    {
        return Author::whereId($id)->update($data);
    }

    public function delete($id)
    {
        Author::destroy($id);
    }

    public function books($id)
    {
        return Book::with('author')->where('author_id', $id)->get();
    }
}
