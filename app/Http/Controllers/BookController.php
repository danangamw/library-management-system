<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Author;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    private BookRepositoryInterface $bookRepositoryInterface;

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)
    {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = $this->bookRepositoryInterface->index();

        return ApiResponseClass::sendResponse(BookResource::collection($books), "List of all books", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $newBook = $request->validated();

        try {
            $book = $this->bookRepositoryInterface->store($newBook);

            return ApiResponseClass::sendResponse(new BookResource($book), 'Book created', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = $this->bookRepositoryInterface->getById($id);
        if (!$book) {
            return ApiResponseClass::sendResponse(null, "Book not found", 404);
        }

        return ApiResponseClass::sendResponse(new BookResource($book), "Book retrieved", 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, $id)
    {
        $book = $this->bookRepositoryInterface->getById($id);
        if (!$book) {
            return ApiResponseClass::sendResponse("Book not found", 404);
        }

        try {
            $data = $request->validated();
            $this->bookRepositoryInterface->update($data, $id);
            $updatedBook = $this->bookRepositoryInterface->getById($id);

            return ApiResponseClass::sendResponse(new BookResource($updatedBook), 'Book successfully updated', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $book = $this->bookRepositoryInterface->getById($id);
        if (!$book) {
            return ApiResponseClass::sendResponse(null, "Book not found", 404);
        }

        $this->bookRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse(null, "Book deleted", 200);
    }
}
