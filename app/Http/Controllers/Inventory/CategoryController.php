<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class CategoryController extends Controller
{

    use LogsActivity;

    public function index(Request $request)
    {
        $search = $request->input('search');

       
        if ($search) {
            $this->logActivity("Search Category", "Keyword: {$search}");
        }

        $categories = Category::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('category_name', 'like', '%' . $search . '%')
                      ->orWhere('category_description', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories->appends(['search' => $search]);

        return view('Inventory.Category.category', compact('categories', 'search'));
    }

    public function create()
    {
        return view('Inventory.Category.create_category');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
        ]);

        $category = Category::create([
            'category_name' => $request->input('category_name'),
            'category_description' => $request->input('category_description'),
        ]);

    
        $this->logActivity("Created Category", [
            "category_id" => $category->id,
            "name"        => $category->category_name
        ]);

        return redirect()->route('inventory.categories')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

   
        $this->logActivity("Viewed Category for Editing", [
            "category_id" => $id,
            "name"        => $category->category_name
        ]);

        return view('Inventory.Category.edit_category', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);

        $oldValues = $category->getOriginal(); 

        $category->update([
            'category_name' => $request->input('category_name'),
            'category_description' => $request->input('category_description'),
        ]);

     
        $this->logActivity("Updated Category", [
            "category_id" => $category->id,
            "old" => $oldValues,
            "new" => $category->getAttributes()
        ]);

        return redirect()->route('inventory.categories')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
      
        $this->logActivity("Deleted Category", [
            "category_id" => $category->id,
            "name"        => $category->category_name
        ]);

        $category->delete();

        return redirect()->route('inventory.categories')->with('success', 'Category deleted successfully.');
    }
}