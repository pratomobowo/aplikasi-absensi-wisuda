<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\KonsumsiRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KonsumsiController extends Controller
{
    public function index(Request $request)
    {
        $query = GraduationTicket::notArchived()->with(['mahasiswa', 'graduationEvent', 'konsumsiRecord.scannedBy']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npm', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest('konsumsi_at')->paginate(50)->withQueryString();

        return view('admin.konsumsi.index', compact('tickets'));
    }

    public function toggle(GraduationTicket $ticket)
    {
        if (is_null($ticket->konsumsi_pagi_at)) {
            $ticket->update([
                'konsumsi_pagi_at' => now(),
                'konsumsi_pagi_by' => auth()->id(),
            ]);
            $message = 'Konsumsi pagi ditandai.';
        } elseif (is_null($ticket->konsumsi_siang_at)) {
            $ticket->update([
                'konsumsi_siang_at' => now(),
                'konsumsi_siang_by' => auth()->id(),
            ]);
            $message = 'Konsumsi siang ditandai.';
        } else {
            $ticket->update([
                'konsumsi_pagi_at' => null,
                'konsumsi_pagi_by' => null,
                'konsumsi_siang_at' => null,
                'konsumsi_siang_by' => null,
            ]);
            $message = 'Konsumsi direset.';
        }

        Log::info('KonsumsiRecord: Manual toggle', [
            'ticket_id' => $ticket->id,
            'mahasiswa_id' => $ticket->mahasiswa_id,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', $message);
    }

    public function reset(GraduationTicket $ticket, string $type)
    {
        if ($type === 'siang' && $ticket->konsumsi_siang_at) {
            $ticket->update([
                'konsumsi_siang_at' => null,
                'konsumsi_siang_by' => null,
            ]);
            $message = 'Konsumsi siang berhasil direset.';
        } elseif ($type === 'pagi' && $ticket->konsumsi_pagi_at && !$ticket->konsumsi_siang_at) {
            $ticket->update([
                'konsumsi_pagi_at' => null,
                'konsumsi_pagi_by' => null,
            ]);
            $message = 'Konsumsi pagi berhasil direset.';
        } else {
            return redirect()->back()->with('error', 'Aksi reset tidak valid.');
        }

        Log::info('KonsumsiRecord: Manual reset', [
            'ticket_id' => $ticket->id,
            'type' => $type,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', $message);
    }

    public function bulkMarkReceived(Request $request)
    {
        $ids = $request->input('ids', []);
        $count = 0;

        DB::transaction(function () use ($ids, &$count) {
            foreach ($ids as $id) {
                $ticket = GraduationTicket::find($id);
                if ($ticket && is_null($ticket->konsumsi_pagi_at)) {
                    $ticket->update([
                        'konsumsi_pagi_at' => now(),
                        'konsumsi_pagi_by' => auth()->id(),
                    ]);
                    $count++;
                }
            }
        });

        Log::info('KonsumsiRecord: Bulk mark as pagi received', [
            'count' => $count,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', $count . ' mahasiswa ditandai konsumsi pagi.');
    }

    public function bulkMarkNotReceived(Request $request)
    {
        $ids = $request->input('ids', []);
        $count = 0;

        DB::transaction(function () use ($ids, &$count) {
            foreach ($ids as $id) {
                $ticket = GraduationTicket::find($id);
                if ($ticket) {
                    $ticket->update([
                        'konsumsi_pagi_at' => null,
                        'konsumsi_pagi_by' => null,
                        'konsumsi_siang_at' => null,
                        'konsumsi_siang_by' => null,
                    ]);
                    $count++;
                }
            }
        });

        Log::info('KonsumsiRecord: Bulk reset', [
            'count' => $count,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', $count . ' mahasiswa direset.');
    }
}