<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Author;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Interfaces\AuthorRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    private AuthorRepositoryInterface $authorRepositoryInterface;

    public function __construct(AuthorRepositoryInterface $authorRepositoryInterface)
    {
        $this->authorRepositoryInterface = $authorRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $authors = $this->authorRepositoryInterface->index();

        return ApiResponseClass::sendResponse(AuthorResource::collection($authors), 'List of all authors', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request): JsonResponse
    {
        $newAuthor = $request->validated();
        DB::beginTransaction();

        try {

            $author = $this->authorRepositoryInterface->store($newAuthor);

            DB::commit();
            return ApiResponseClass::sendResponse(new AuthorResource($author), 'Author created', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $author = $this->authorRepositoryInterface->getById($id);
        if (!$author) {
            return ApiResponseClass::sendResponse(null, "Author not found", 404);
        }

        return ApiResponseClass::sendResponse(new AuthorResource($author), "Author retrieved", 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request,  $id): JsonResponse
    {
        try {

            $author = $this->authorRepositoryInterface->getById($id);
            if (!$author) {
                return ApiResponseClass::sendResponse("Author not found", 404);
            }

            $data = $request->validated();

            $this->authorRepositoryInterface->update($data, $id);
            $updatedAuthor = $this->authorRepositoryInterface->getById($id);

            if (!$updatedAuthor) {
                DB::rollBack();
                return ApiResponseClass::sendResponse("Failed to update author", 500);
            }

            return ApiResponseClass::sendResponse($updatedAuthor, 'Author successfully updated', 201);
        } catch (\Exception $ex) {
            return  ApiResponseClass::sendResponse(null, "something went wrong", 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $author = $this->authorRepositoryInterface->getById($id);
        if (!$author) {
            return ApiResponseClass::sendResponse(null, "Author not found", 404);
        }

        $this->authorRepositoryInterface->delete($id);


        return ApiResponseClass::sendResponse(null, "Author successfully deleted", 200);
    }


    public function getBooks($id): JsonResponse
    {
        $books = $this->authorRepositoryInterface->books($id);
        if (!$books) {
            return ApiResponseClass::sendResponse("Book with author id ${id} not found", 404);
        }

        return ApiResponseClass::sendResponse(BookResource::collection($books), "Books with author id ${id}", 200);
    }
}
