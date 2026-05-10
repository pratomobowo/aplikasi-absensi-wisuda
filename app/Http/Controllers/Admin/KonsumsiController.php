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

        if ($request->has('konsumsi_diterima') && $request->input('konsumsi_diterima') !== '') {
            $query->where('konsumsi_diterima', $request->boolean('konsumsi_diterima'));
        }

        if ($request->filled('graduation_event_id')) {
            $query->where('graduation_event_id', $request->input('graduation_event_id'));
        }

        if ($request->filled('scanned_from')) {
            $query->whereDate('konsumsi_at', '>=', $request->input('scanned_from'));
        }

        if ($request->filled('scanned_until')) {
            $query->whereDate('konsumsi_at', '<=', $request->input('scanned_until'));
        }

        $tickets = $query->latest('konsumsi_at')->paginate(50)->withQueryString();
        $events = GraduationEvent::where('status', '!=', 'completed')->pluck('name', 'id');

        return view('admin.konsumsi.index', compact('tickets', 'events'));
    }

    public function toggle(GraduationTicket $ticket)
    {
        $oldStatus = $ticket->konsumsi_diterima;
        $newStatus = !$oldStatus;

        if ($newStatus) {
            $ticket->update([
                'konsumsi_diterima' => true,
                'konsumsi_at' => now(),
            ]);

            if (!$ticket->konsumsiRecord()->exists()) {
                KonsumsiRecord::create([
                    'graduation_ticket_id' => $ticket->id,
                    'scanned_by' => auth()->id(),
                    'scanned_at' => now(),
                ]);
            }

            Log::info('KonsumsiRecord: Manual toggle to received', [
                'ticket_id' => $ticket->id,
                'mahasiswa_id' => $ticket->mahasiswa_id,
                'admin_id' => auth()->id(),
                'previous_status' => $oldStatus,
            ]);
        } else {
            $ticket->update([
                'konsumsi_diterima' => false,
                'konsumsi_at' => null,
            ]);

            $ticket->konsumsiRecord()->delete();

            Log::info('KonsumsiRecord: Manual toggle to not received', [
                'ticket_id' => $ticket->id,
                'mahasiswa_id' => $ticket->mahasiswa_id,
                'admin_id' => auth()->id(),
                'previous_status' => $oldStatus,
            ]);
        }

        return redirect()->back()->with('success', $newStatus ? 'Konsumsi ditandai sebagai sudah diterima.' : 'Konsumsi ditandai sebagai belum diterima.');
    }

    public function bulkMarkReceived(Request $request)
    {
        $ids = $request->input('ids', []);

        DB::transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $ticket = GraduationTicket::find($id);
                if ($ticket && !$ticket->konsumsi_diterima) {
                    $ticket->update([
                        'konsumsi_diterima' => true,
                        'konsumsi_at' => now(),
                    ]);

                    if (!$ticket->konsumsiRecord()->exists()) {
                        KonsumsiRecord::create([
                            'graduation_ticket_id' => $ticket->id,
                            'scanned_by' => auth()->id(),
                            'scanned_at' => now(),
                        ]);
                    }
                }
            }
        });

        Log::info('KonsumsiRecord: Bulk mark as received', [
            'count' => count($ids),
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', count($ids) . ' mahasiswa ditandai sudah menerima konsumsi.');
    }

    public function bulkMarkNotReceived(Request $request)
    {
        $ids = $request->input('ids', []);

        DB::transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $ticket = GraduationTicket::find($id);
                if ($ticket && $ticket->konsumsi_diterima) {
                    $ticket->update([
                        'konsumsi_diterima' => false,
                        'konsumsi_at' => null,
                    ]);

                    $ticket->konsumsiRecord()->delete();
                }
            }
        });

        Log::info('KonsumsiRecord: Bulk mark as not received', [
            'count' => count($ids),
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', count($ids) . ' mahasiswa ditandai belum menerima konsumsi.');
    }
}