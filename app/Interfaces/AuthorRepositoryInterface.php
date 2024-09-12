<?php

namespace App\Interfaces;

interface AuthorRepositoryInterface
{
    public function index($filters, $perPage);
    public function getById($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function books($id);
}
