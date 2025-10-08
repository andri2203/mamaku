<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all suppliers from database and sort by name ascending
        $suppliers = \App\Models\Supplier::orderBy('name', 'asc')->get();
        $data = [
            'title' => 'Suppliers',
            'suppliers' => $suppliers,
        ];
        return view('suppliers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // define data array
        $data = [
            'title' => 'Tambah Supplier',
        ];
        return view('suppliers.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        // create new supplier with try catch
        try {
            Supplier::create($validated);
            return redirect()->route('supplier.index')->with('success', 'Supplier added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add supplier: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // find supplier by id
        $supplier = Supplier::findOrFail($id);
        $data = [
            'title' => 'Edit Supplier',
            'supplier' => $supplier,
        ];
        return view('suppliers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // find supplier by id
        $supplier = Supplier::findOrFail($id);

        // validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        // update supplier with try catch
        try {
            $supplier->update($validated);
            return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update supplier: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // find supplier by id
        $supplier = Supplier::findOrFail($id);

        // delete supplier with try catch
        try {
            $supplier->delete();
            return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }
}
