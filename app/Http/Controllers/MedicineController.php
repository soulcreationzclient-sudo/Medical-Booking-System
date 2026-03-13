<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use Auth;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::where('hospital_id', auth()->user()->hospital_id)->get();
        return view('hospital_admin.medicines.index', compact('medicines'));
    }

    public function store(Request $request)
    {
        Medicine::create([
            'hospital_id' => auth()->user()->hospital_id,
            'name' => $request->name,
            'dosage' => $request->dosage,
            'price' => $request->price,
        ]);

        return redirect()->back()->with('success','Medicine added');
    }

    public function getPrice($id)
    {
        $medicine = Medicine::find($id);
        return response()->json([
            'price' => $medicine->price
        ]);
    }
}
