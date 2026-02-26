<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return response()->json(Table::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required',
            'capacity' => 'required|numeric'
        ]);

        $table = Table::create($request->all());

        return response()->json($table, 201);
    }

    public function show($id)
    {
        return Table::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->update($request->all());

        return response()->json($table);
    }

    public function destroy($id)
    {
        Table::destroy($id);
        return response()->json(['message' => 'data berhasil dihapus']);
    }
}