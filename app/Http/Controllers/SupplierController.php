<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('projects');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $suppliers = $query->latest()->paginate(10)->withQueryString();
        
        return Inertia::render('SuperAdmin/Suppliers/Index', compact('suppliers'));
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Suppliers/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:suppliers',
            'company_name' => 'required',
            'contact_person' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);
        
        Supplier::create($data);
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return Inertia::render('SuperAdmin/Suppliers/Edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $data = $request->validate([
            'code' => 'required|unique:suppliers,code,' . $id,
            'company_name' => 'required',
            'contact_person' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);
        
        $supplier->update($data);
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // Check if supplier has projects
        if ($supplier->projects()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with existing projects');
        }
        
        $supplier->delete();
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully');
    }
}
