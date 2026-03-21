<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::where('hospital_id', auth()->user()->hospital_id)->get();
        return view('hospital_admin.medicines.index', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'dosage' => 'nullable|string|max:100',
            'price'  => 'required|numeric|min:0',
        ]);

        Medicine::create([
            'hospital_id' => auth()->user()->hospital_id,
            'name'        => $request->name,
            'dosage'      => $request->dosage,
            'price'       => $request->price,
        ]);

        return redirect()->route('hospital_admin.medicines.index')
                         ->with('success', 'Medicine added successfully.');
    }

    public function getPrice($id)
    {
        $medicine = Medicine::find($id);
        if (!$medicine) {
            return response()->json(['price' => null], 404);
        }
        return response()->json([
            'price'  => $medicine->price,
            'dosage' => $medicine->dosage,
            'name'   => $medicine->name,
        ]);
    }
}