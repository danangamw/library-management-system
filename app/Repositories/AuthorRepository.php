<?php

namespace App\Repositories;

use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Facades\Redis;

class AuthorRepository implements AuthorRepositoryInterface
{
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
        $cachedAuthor = Redis::get("author_{$id}");

        if (isset($cachedAuthor)) {
            $author = json_decode($cachedAuthor, false);

            return $author;
        } else {
            $author = Author::find($id);
            Redis::set("author_$id", $author);

            return $author;
        }
    }

    public function store(array $data)
    {
        $newAuthor =
            Author::create($data);
        Redis::set("author_{$newAuthor->id}", $newAuthor);
        return $newAuthor;
    }

    public function update(array $data, $id)
    {
        Redis::del("author_$id");
        $author = Author::find($id);
        if ($author) {
            $updatedAuthor = Author::whereId($id)->update($data);
            Redis::set("author_{$updatedAuthor->id}", $updatedAuthor);
            return $updatedAuthor;
        }

        return null;
    }

    public function delete($id)
    {
        Redis::del("author_$id");
        Author::destroy($id);
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
