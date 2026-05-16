<?php

namespace App\Http\Controllers\Staff;

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
    // Level 1 — All categories
    public function index()
    {
        $categories = Category::withCount('subcategories')->get();
        return view('staff.inventory.index', compact('categories'));
    }

    // Level 2 — Subcategories under a category
    public function showCategory($id)
    {
        $category      = Category::findOrFail($id);
        $subcategories = Subcategory::withCount('items')
            ->where('category_id', $id)->get();
        return view('staff.inventory.show_category', compact('category', 'subcategories'));
    }

    // Level 3 — Items under a subcategory
    public function showSubcategory($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        $items       = Item::with('inventory')
            ->where('subcategory_id', $id)->get();
        return view('staff.inventory.show_subcategory', compact('subcategory', 'items'));
    }

    // Category CRUD
    public function createCategory()
    {
        return view('staff.inventory.create_category');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        Category::create($validated);

        return redirect()->route('staff.inventory.index')
            ->with('success', 'Category created successfully.');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('staff.inventory.edit_category', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        $category->update($validated);

        return redirect()->route('staff.inventory.index')
            ->with('success', 'Category updated successfully.');
    }

    // Subcategory CRUD
    public function createSubcategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('staff.inventory.create_subcategory', compact('category'));
    }

    public function storeSubcategory(Request $request, $categoryId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $validated['category_id'] = $categoryId;
        Subcategory::create($validated);

        return redirect()->route('staff.inventory.category.show', $categoryId)
            ->with('success', 'Subcategory created successfully.');
    }

    public function editSubcategory($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return view('staff.inventory.edit_subcategory', compact('subcategory'));
    }

    public function updateSubcategory(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $subcategory->update($validated);

        return redirect()->route('staff.inventory.category.show', $subcategory->category_id)
            ->with('success', 'Subcategory updated successfully.');
    }

    // Item CRUD
    public function createItem($subcategoryId)
    {
        $subcategory = Subcategory::with('category')->findOrFail($subcategoryId);
        return view('staff.inventory.create_item', compact('subcategory'));
    }

    public function storeItem(Request $request, $subcategoryId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['subcategory_id'] = $subcategoryId;
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
            $validated['image'] = $imagePath;
        }

        $item = Item::create($validated);

        // Create inventory record
        Inventory::create([
            'item_id' => $item->id,
            'quantity' => $validated['quantity'],
            'expiration_date' => $validated['expiration_date'] ?? null,
            'last_updated' => now()
        ]);

        NotificationService::inventoryAdded($item->id, auth()->id());

        return redirect()->route('staff.inventory.subcategory.show', $subcategoryId)
            ->with('success', 'Item created successfully.');
    }

    public function editItem($id)
    {
        $item = Item::with(['subcategory.category', 'inventory'])->findOrFail($id);
        return view('staff.inventory.edit_item', compact('item'));
    }

    public function updateItem(Request $request, $id)
    {
        $item = Item::with('inventory')->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->file('image')->store('items', 'public');
            $validated['image'] = $imagePath;
        }

        $item->update($validated);

        // Update inventory record
        if ($item->inventory) {
            $item->inventory->update([
                'quantity' => $validated['quantity'],
                'expiration_date' => $validated['expiration_date'] ?? null,
                'last_updated' => now()
            ]);
        }

        return redirect()->route('staff.inventory.subcategory.show', $item->subcategory_id)
            ->with('success', 'Item updated successfully.');
    }

    // Category destroy
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has subcategories
        if ($category->subcategories()->count() > 0) {
            return redirect()->route('inventory.index')
                ->with('error', 'Cannot delete category with existing subcategories.');
        }
        
        $category->delete();
        
        return redirect()->route('inventory.index')
            ->with('success', 'Category deleted successfully.');
    }

    // Subcategory destroy
    public function destroySubcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        
        // Check if subcategory has items
        if ($subcategory->items()->count() > 0) {
            return redirect()->route('inventory.category.show', $subcategory->category_id)
                ->with('error', 'Cannot delete subcategory with existing items.');
        }
        
        $subcategory->delete();
        
        return redirect()->route('inventory.category.show', $subcategory->category_id)
            ->with('success', 'Subcategory deleted successfully.');
    }

    // Item destroy
    public function destroyItem($id)
    {
        $item = Item::with('subcategory')->findOrFail($id);
        $categoryId = $item->subcategory->category_id;
        
        // Delete inventory record if exists
        if ($item->inventory) {
            $item->inventory->delete();
        }
        
        $item->delete();
        
        return redirect()->route('inventory.category.show', $categoryId)
            ->with('success', 'Item deleted successfully.');
    }
}
