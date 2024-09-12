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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    private AuthorRepositoryInterface $authorRepositoryInterface;

    public function __construct(AuthorRepositoryInterface $authorRepositoryInterface)
    {
        $this->authorRepositoryInterface = $authorRepositoryInterface;
    }

    /**
     * Display a listing of the authors.
     * 
     * @group Authors
     * @queryParam search required Comma-separated list of fields to include in the response.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = request()->only(['search']);
        $perPage = request()->get('per_page', 10);
        $authors = $this->authorRepositoryInterface->index($filters, $perPage);

        return ApiResponseClass::sendResponse($authors, 'List of all authors', 200);
    }

    /**
     * Store a newly created author in storage.
     * 
     * @group Authors
     * @param StoreAuthorRequest $request
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
     * Display the specified author.
     * 
     * @group Authors
     * @urlParam id integer required The ID of the author. Example: 1
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
     * Update the specified author in storage.
     * 
     * @group Authors
     * @urlParam id integer required The ID of the author. Example: 1
     * @param UpdateAuthorRequest $request
     * @response {
     *   "message": "Author successfully updated"
     * }
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
     * Remove the specified author from storage.
     * 
     * @group Authors
     * @urlParam id integer required The ID of the author. Example: 1
     * @response {
     *   "message": "Author successfully deleted"
     * }
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

    /**
     * Display a listing of the book within specific author.
     * 
     * @group Associations
     * @urlParam id integer required The ID of the author. Example: 1
     */
    public function getBooks($id): JsonResponse
    {
        $books = $this->authorRepositoryInterface->books($id);
        if (!$books) {
            return ApiResponseClass::sendResponse("Book with author id ${id} not found", 404);
        }

        return ApiResponseClass::sendResponse(BookResource::collection($books), "Books with author id ${id}", 200);
    }
}
