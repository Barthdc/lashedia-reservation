<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stylist;
use Illuminate\Http\Request;

class StylistController extends Controller
{
    public function index()
    {
        $stylists = Stylist::latest()->get();

        return view('admin.stylists.index', compact('stylists'));
    }

    public function create()
    {
        return view('admin.stylists.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'specialist_1' => 'nullable',
            'specialist_2' => 'nullable',
            'specialist_3' => 'nullable',
            'image' => 'required|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stylists', 'public');
        }

        Stylist::create($data);

        return redirect()->route('admin.stylists.index');
    }

    public function edit(Stylist $stylist)
    {
        return view('admin.stylists.edit', compact('stylist'));
    }

    public function update(Request $request, Stylist $stylist)
    {
        $data = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'specialist_1' => 'nullable',
            'specialist_2' => 'nullable',
            'specialist_3' => 'nullable',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stylists', 'public');
        }

        $stylist->update($data);

        return redirect()->route('admin.stylists.index');
    }

    public function destroy(Stylist $stylist)
    {
        $stylist->delete();

        return back();
    }
}
