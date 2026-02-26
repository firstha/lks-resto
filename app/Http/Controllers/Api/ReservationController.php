<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return Reservation::with('table')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required',
            'reservation_time' => 'required|date'
        ]);

        // CEK APAKAH MEJA SUDAH DIPESAN DI JAM YANG SAMA
        $exists = Reservation::where('table_id', $request->table_id)
            ->where('reservation_time', $request->reservation_time)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Meja sudah dipesan pada waktu tersebut!'
            ], 400);
        }

        $reservation = Reservation::create($request->all());

        // Ubah status meja jadi reserved
        Table::where('id', $request->table_id)
            ->update(['status' => 'reserved']);

        return response()->json($reservation, 201);
    }

    public function destroy($id)
    {
        Reservation::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
