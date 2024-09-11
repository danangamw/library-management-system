<?php

namespace App\Providers;

use App\Interfaces\AuthorRepositoryInterface;
use App\Interfaces\BookRepositoryInterface;
use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Binding AuthorRepositoryInterface to AuthorRepository
        $this->app->bind(
            AuthorRepositoryInterface::class,
            AuthorRepository::class
        );

        // Binding BookRepositoryinterface to BookRepository
        $this->app->bind(
            BookRepositoryInterface::class,
            BookRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
