<?php

namespace App\Interfaces;

interface BookRepositoryInterface
{
    public function index(array $filters, $perPage = 25);
    public function getById($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
