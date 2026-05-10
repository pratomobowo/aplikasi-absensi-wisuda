<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GraduationTicketsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGraduationEventRequest;
use App\Http\Requests\UpdateGraduationEventRequest;
use App\Models\GraduationEvent;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GraduationEventController extends Controller
{
    public function index(Request $request)
    {
        $query = GraduationEvent::withCount('graduationTickets');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->input('search')}%");
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_until')) {
            $query->whereDate('date', '<=', $request->input('date_until'));
        }

        $events = $query->latest('date')->paginate(15)->withQueryString();

        return view('admin.graduation-events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.graduation-events.create');
    }

    public function store(StoreGraduationEventRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('feature_image')) {
            $path = $request->file('feature_image')->store('event-features', 'public');
            $data['feature_image'] = basename($path);
        }

        GraduationEvent::create($data);

        return redirect()->route('admin.graduation-events.index')->with('success', 'Acara wisuda berhasil ditambahkan.');
    }

    public function edit(GraduationEvent $graduationEvent)
    {
        return view('admin.graduation-events.edit', compact('graduationEvent'));
    }

    public function update(UpdateGraduationEventRequest $request, GraduationEvent $graduationEvent)
    {
        $data = $request->validated();

        if ($request->hasFile('feature_image')) {
            if ($graduationEvent->feature_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('event-features/' . $graduationEvent->feature_image);
            }
            $path = $request->file('feature_image')->store('event-features', 'public');
            $data['feature_image'] = basename($path);
        }

        $graduationEvent->update($data);

        return redirect()->route('admin.graduation-events.index')->with('success', 'Acara wisuda berhasil diperbarui.');
    }

    public function destroy(GraduationEvent $graduationEvent)
    {
        $graduationEvent->delete();

        return redirect()->route('admin.graduation-events.index')->with('success', 'Acara wisuda berhasil dihapus.');
    }

    public function setActive(GraduationEvent $graduationEvent)
    {
        // Deactivate all other events and set them to upcoming
        GraduationEvent::where('id', '!=', $graduationEvent->id)
            ->where('status', '!=', 'completed')
            ->update(['is_active' => false, 'status' => 'upcoming']);
        
        // Set this event as active
        $graduationEvent->update(['is_active' => true, 'status' => 'active']);

        return redirect()->route('admin.graduation-events.index')->with('success', 'Acara wisuda diatur sebagai aktif.');
    }

    public function setStatus(Request $request, GraduationEvent $graduationEvent)
    {
        $request->validate([
            'status' => ['required', 'in:upcoming,active,completed'],
        ]);

        $status = $request->input('status');
        $updates = ['status' => $status];

        if ($status === 'active') {
            // Set is_active to true and deactivate others
            GraduationEvent::where('id', '!=', $graduationEvent->id)
                ->where('status', '!=', 'completed')
                ->update(['is_active' => false, 'status' => 'upcoming']);
            $updates['is_active'] = true;
        } elseif ($status === 'completed') {
            $updates['is_active'] = false;
        } else {
            $updates['is_active'] = false;
        }

        $graduationEvent->update($updates);

        // Archive all related data if status is completed
        if ($status === 'completed') {
            $now = now();
            
            // Archive all graduation tickets for this event
            \App\Models\GraduationTicket::where('graduation_event_id', $graduationEvent->id)
                ->whereNull('archived_at')
                ->update(['archived_at' => $now]);
            
            // Archive all attendances for tickets in this event
            \App\Models\Attendance::whereHas('graduationTicket', function ($q) use ($graduationEvent) {
                $q->where('graduation_event_id', $graduationEvent->id);
            })
            ->whereNull('archived_at')
            ->update(['archived_at' => $now]);
            
            // Archive all konsumsi records for tickets in this event
            \App\Models\KonsumsiRecord::whereHas('graduationTicket', function ($q) use ($graduationEvent) {
                $q->where('graduation_event_id', $graduationEvent->id);
            })
            ->whereNull('archived_at')
            ->update(['archived_at' => $now]);
        }

        $labels = [
            'upcoming' => 'Akan Datang',
            'active' => 'Aktif',
            'completed' => 'Selesai',
        ];

        return redirect()->route('admin.graduation-events.index')
            ->with('success', "Status acara wisuda diubah menjadi {$labels[$status]}.");
    }

    public function generateTickets(GraduationEvent $graduationEvent)
    {
        $ticketService = app(TicketService::class);
        $result = $ticketService->generateTicketsForEvent($graduationEvent, null, true);

        $message = "Dibuat: {$result['created']} | Lewat: {$result['skipped']} | Gagal: {$result['failed']}";
        $type = $result['failed'] === 0 ? 'success' : 'warning';

        return redirect()->route('admin.graduation-events.index')->with($type, "Tiket dibuat. {$message}");
    }

    public function exportTickets(GraduationEvent $graduationEvent)
    {
        $cleanName = str_replace(['/', '\\'], '-', $graduationEvent->name);
        $fileName = 'Tiket-Wisuda-' . $cleanName . '-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new GraduationTicketsExport($graduationEvent), $fileName);
    }
}