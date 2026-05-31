<?php

namespace App\Http\Controllers;

use App\Models\ChargingQueue;
use App\Models\ChargerMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargingQueueController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'charger_machine_id' => 'required|exists:charger_machines,id',
        ]);

        $machine = ChargerMachine::findOrFail($request->charger_machine_id);

        if (strtolower($machine->status) === 'available') {
            return redirect()
                ->route('rider.transactions.prepare', $machine->id)
                ->with('success', 'Mesin tersedia, Anda dapat langsung memulai pengisian.');
        }

        if (strtolower($machine->status) === 'maintenance') {
            return redirect()
                ->back()
                ->with('error', 'Mesin sedang dalam perbaikan dan tidak dapat menerima antrean.');
        }

        $existingQueue = ChargingQueue::where('user_id', Auth::id())
            ->where('charger_machine_id', $machine->id)
            ->where('status', 'waiting')
            ->first();

        if ($existingQueue) {
            $queuePosition = ChargingQueue::where('charger_machine_id', $machine->id)
                ->where('status', 'waiting')
                ->where('id', '<=', $existingQueue->id)
                ->count();

            return redirect()
                ->back()
                ->with('success', 'Anda sudah berada dalam antrean. Nomor antrean Anda: ' . $queuePosition . '.');
        }

        $queue = ChargingQueue::create([
            'user_id' => Auth::id(),
            'charger_machine_id' => $machine->id,
            'status' => 'waiting',
            'queued_at' => now(),
        ]);

        $queuePosition = ChargingQueue::where('charger_machine_id', $machine->id)
            ->where('status', 'waiting')
            ->where('id', '<=', $queue->id)
            ->count();

        return redirect()
            ->back()
            ->with('success', 'Berhasil masuk antrean digital. Nomor antrean Anda: ' . $queuePosition . '.');
    }

    public function cancel($id)
    {
        $queue = ChargingQueue::where('user_id', Auth::id())
            ->where('status', 'waiting')
            ->findOrFail($id);

        $queue->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Antrean berhasil dibatalkan.');
    }
}