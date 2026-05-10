<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GraduationTicketsExport;
use App\Http\Controllers\Controller;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GraduationTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = GraduationTicket::notArchived()->with(['mahasiswa', 'graduationEvent', 'attendances']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npm', 'like', "%{$search}%");
            });
        }

        if ($request->filled('graduation_event_id')) {
            $query->where('graduation_event_id', $request->input('graduation_event_id'));
        }

        if ($request->has('is_distributed') && $request->input('is_distributed') !== '') {
            $query->where('is_distributed', $request->boolean('is_distributed'));
        }

        if ($request->filled('attendance_status')) {
            match ($request->input('attendance_status')) {
                'all_attended' => $query->whereHas('attendances', function ($q) {
                    $q->selectRaw('graduation_ticket_id')->groupBy('graduation_ticket_id')->havingRaw('COUNT(*) = 3');
                }),
                'partial_attended' => $query->whereHas('attendances', function ($q) {
                    $q->selectRaw('graduation_ticket_id')->groupBy('graduation_ticket_id')->havingRaw('COUNT(*) > 0 AND COUNT(*) < 3');
                }),
                'not_attended' => $query->doesntHave('attendances'),
                default => $query,
            };
        }

        $tickets = $query->latest()->paginate(20)->withQueryString();
        $events = GraduationEvent::where('status', '!=', 'completed')->pluck('name', 'id');

        return view('admin.graduation-tickets.index', compact('tickets', 'events'));
    }

    public function show(GraduationTicket $graduationTicket)
    {
        $graduationTicket->load(['mahasiswa', 'graduationEvent', 'attendances.scannedBy']);
        return view('admin.graduation-tickets.show', compact('graduationTicket'));
    }

    public function create()
    {
        $events = GraduationEvent::pluck('name', 'id');
        $mahasiswas = Mahasiswa::pluck('nama', 'id');
        return view('admin.graduation-tickets.create', compact('events', 'mahasiswas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'graduation_event_id' => ['required', 'exists:graduation_events,id'],
        ]);

        $ticketService = app(TicketService::class);
        $mahasiswa = Mahasiswa::find($data['mahasiswa_id']);
        $event = GraduationEvent::find($data['graduation_event_id']);

        $ticketService->createTicket($mahasiswa, $event);

        return redirect()->route('admin.graduation-tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    public function destroy(GraduationTicket $graduationTicket)
    {
        $graduationTicket->delete();
        return redirect()->route('admin.graduation-tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $ticketIds = $request->input('ticket_ids', []);
        $fileName = 'Tiket-Wisuda-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new GraduationTicketsExport(null, $ticketIds), $fileName);
    }

    public function bulkCreate(Request $request)
    {
        $data = $request->validate([
            'mahasiswa_ids' => ['required', 'array'],
            'mahasiswa_ids.*' => ['exists:mahasiswa,id'],
            'graduation_event_id' => ['required', 'exists:graduation_events,id'],
        ]);

        $ticketService = app(TicketService::class);
        $event = GraduationEvent::find($data['graduation_event_id']);

        foreach ($data['mahasiswa_ids'] as $mahasiswaId) {
            $mahasiswa = Mahasiswa::find($mahasiswaId);
            $ticketService->createTicket($mahasiswa, $event);
        }

        return redirect()->route('admin.graduation-tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }
}