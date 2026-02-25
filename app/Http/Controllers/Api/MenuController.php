<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return response()->json(Menu::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        $menu = Menu::create($request->all());

        return response()->json($menu, 201);
    }

    public function show($id)
    {
        return Menu::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->all());

        return response()->json($menu);
    }

    public function destroy($id)
    {
        Menu::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
