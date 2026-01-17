<?php

namespace App\Services\POS;

use App\Models\Products;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductSearchService
{
    /**
     * Search and filter products with pagination
     * 
     * @param string|null $search
     * @param int|null $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchProducts(?string $search = null, ?int $categoryId = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Products::query();


        if ($search) {
            $escapedSearch = addcslashes($search, '%_');
            $query->where(function($q) use ($escapedSearch) {
                $q->where('product_name', 'like', '%' . $escapedSearch . '%')
                  ->orWhere('product_description', 'like', '%' . $escapedSearch . '%');
            });
        }

  
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }


        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all categories for filtering
     * 
     * @return Collection
     */
    public function getAllCategories()
    {
        return Category::select('id', 'category_name')->get();
    }
}

