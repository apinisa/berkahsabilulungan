<?php

namespace App\Http\Controllers;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class SupplierController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $query = Supplier::query();

    if ($search) {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
    }

    $suppliers = $query->orderBy('supplier_id', 'asc')->get();


    return view('suppliers.index', compact('suppliers'));
}


    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:suppliers,email',
            'address' => 'nullable|string',
        ]);

        // Ambil ID terakhir
    $lastSupplier = Supplier::orderBy('supplier_id', 'desc')->first();
    if (!$lastSupplier) {
        $newId = 'SP001';
    } else {
        $number = intval(substr($lastSupplier->supplier_id, 2)) + 1;
        $newId = 'SP' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    Supplier::create([
        'supplier_id' => $newId,
        'name' => $request->name,
        'contact_person' => $request->contact_person,
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
    ]);

    return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
}

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => [
                        'required',
                        'email',
        Rule::unique('suppliers')->ignore($supplier->supplier_id, 'supplier_id'),
        ],

            'address' => 'nullable|string',
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
