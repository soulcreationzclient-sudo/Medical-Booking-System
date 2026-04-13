<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AiSearchController extends Controller
{
    public function index()
    {
        return view('hospital_admin.ai_search.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        if (!$user || !$user->hospital_id) {
            return back()->with('error', 'Hospital not found for current user.');
        }

        $queryText = $request->input('query');

        try {
            $response = Http::timeout(30)->post(config('services.ai_search.url') . '/query', [
                'hospital_id' => $user->hospital_id,
                'query' => $queryText,
                'today' => now()->toDateString(),
            ]);

            if (!$response->successful()) {
                return back()
                    ->withInput()
                    ->with('error', 'AI search request failed: ' . $response->body());
            }

            $data = $response->json();

            return view('hospital_admin.ai_search.index', [
                'query' => $queryText,
                'searchResult' => $data,
            ]);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Unable to connect to AI search service. ' . $e->getMessage());
        }
    }
}