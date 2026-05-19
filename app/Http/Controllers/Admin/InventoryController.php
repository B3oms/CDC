<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Item;
use App\Models\Inventory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /**
     * Generate a random color for inventory containers
     */
    public function generateRandomColor()
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2',
            '#F8B739', '#52B788', '#E76F51', '#8E7CC3', '#F4A261',
            '#E9C46A', '#2A9D8F', '#E63946', '#A8DADC', '#457B9D',
            '#1D3557', '#F1FAEE', '#A8DADC', '#457B9D', '#1D3557',
            '#FF006E', '#FB5607', '#FFBE0B', '#8338EC', '#3A86FF'
        ];
        
        return $colors[array_rand($colors)];
    }

    // Level 1 — All categories
    public function index()
    {
        $categories = Category::withCount('subcategories')->get();
        return view('admin.inventory.index', compact('categories'));
    }

    // Level 2 — Subcategories under a category
    public function showCategory($id)
    {
        $category      = Category::findOrFail($id);
        $subcategories = Subcategory::withCount('items')
            ->where('category_id', $id)->get();
        return view('admin.inventory.show_category', compact('category', 'subcategories'));
    }

    // Level 3 — Items under a subcategory
    public function showSubcategory($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        $items       = Item::with('inventory')
            ->where('subcategory_id', $id)->get();
        return view('admin.inventory.show_subcategory', compact('subcategory', 'items'));
    }

    // Category CRUD
    public function createCategory()
    {
        return view('admin.inventory.create_category');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $this->generateRandomColor(),
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Category created successfully.');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.inventory.edit_category', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Category updated.');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Category deleted.');
    }

    // Subcategory CRUD
    public function createSubcategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('admin.inventory.create_subcategory', compact('category'));
    }

    public function storeSubcategory(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Subcategory::create([
            'category_id' => $categoryId,
            'name'        => $request->name,
            'color'       => $this->generateRandomColor(),
        ]);

        return redirect()->route('admin.inventory.category.show', $categoryId)
            ->with('success', 'Subcategory created.');
    }

    public function editSubcategory($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return view('admin.inventory.edit_subcategory', compact('subcategory'));
    }

    public function updateSubcategory(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $subcategory->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.inventory.category.show', $subcategory->category_id)
            ->with('success', 'Subcategory updated.');
    }

    public function destroySubcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);
                $categoryId = $subcategory->category_id;
        $subcategory->delete();

        return redirect()->route('admin.inventory.category.show', $categoryId)
            ->with('success', 'Subcategory deleted.');
    }

    // Item CRUD
    public function createItem($subcategoryId)
    {
        $subcategory = Subcategory::with('category')->findOrFail($subcategoryId);
        return view('admin.inventory.create_item', compact('subcategory'));
    }

    public function storeItem(Request $request, $subcategoryId)
    {
        $subcategory = Subcategory::findOrFail($subcategoryId);

        $request->validate([
            'name'            => 'required|string|max:150',
            'description'     => 'nullable|string',
            'unit'            => 'required|string|max:50',
            'quantity'        => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $item = Item::create([
            'category_id'    => $subcategory->category_id,
            'subcategory_id' => $subcategoryId,
            'name'           => $request->name,
            'description'    => $request->description,
            'unit'           => $request->unit,
            'color'          => $this->generateRandomColor(),
        ]);

        Inventory::create([
            'item_id'         => $item->id,
            'quantity'        => $request->quantity,
            'expiration_date' => $request->expiration_date,
        ]);

        // Trigger notification for inventory addition
        NotificationService::inventoryAdded($item->id, auth()->id());

        return redirect()->route('admin.inventory.subcategory', $subcategoryId)
            ->with('success', 'Item added successfully.');
    }

    public function editItem($id)
    {
        $item        = Item::with(['inventory', 'subcategory.category'])->findOrFail($id);
        $subcategory = $item->subcategory;
        return view('admin.inventory.edit_item', compact('item', 'subcategory'));
    }

    public function updateItem(Request $request, $id)
    {
        $item = Item::with('inventory')->findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:150',
            'description'     => 'nullable|string',
            'unit'            => 'required|string|max:50',
            'quantity'        => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $item->update([
            'name'        => $request->name,
            'description' => $request->description,
            'unit'        => $request->unit,
        ]);

        if ($item->inventory) {
            $item->inventory->update([
                'quantity'        => $request->quantity,
                'expiration_date' => $request->expiration_date,
            ]);
        } else {
            Inventory::create([
                'item_id'         => $item->id,
                'quantity'        => $request->quantity,
                'expiration_date' => $request->expiration_date,
            ]);
        }

        return redirect()->route('admin.inventory.subcategory', $item->subcategory_id)
            ->with('success', 'Item updated.');
    }

    public function destroyItem($id)
    {
        $item          = Item::findOrFail($id);
        $subcategoryId = $item->subcategory_id;
        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();

        return redirect()->route('admin.inventory.subcategory', $subcategoryId)
            ->with('success', 'Item deleted.');
    }
}