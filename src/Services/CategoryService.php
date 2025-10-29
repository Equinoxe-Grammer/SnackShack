<?php
namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categories;

    public function __construct(?CategoryRepository $categories = null)
    {
        $this->categories = $categories ?? new CategoryRepository();
    }

    public function list(): array
    {
        return $this->categories->findAll();
    }
}
