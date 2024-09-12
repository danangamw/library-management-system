<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    private BookRepositoryInterface $bookRepositoryInterface;

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)
    {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }

    /**
     * Display a listing of the Books.
     * 
     * @group Books
     * @queryParam search required Comma-separated list of fields to include in the response.
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $filters = request()->only(['search']);
        $perPage = request()->get('per_page', 10);
        $books = $this->bookRepositoryInterface->index($filters, $perPage);

        return ApiResponseClass::sendResponse($books, "List of all books", 200);
    }

    /**
     * Store a newly created book in storage.
     * 
     * @group Books
     * 
     * @param StoreBookRequest $request
     * @return JsonResponse
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
     * Display the specified book.
     *
     * @group Books
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $book = $this->bookRepositoryInterface->getById($id);
        if (!$book) {
            return ApiResponseClass::sendResponse(null, "Book not found", 404);
        }

        return ApiResponseClass::sendResponse(new BookResource($book), "Book retrieved", 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @group Books
     * @param UpdateBookRequest $request
     * @param int $id
     * @return JsonResponse
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
     * 
     * @group Books
     * @param int $id
     * @return JsonResponse
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
