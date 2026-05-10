<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GraduationEvent;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::notArchived()-with([
            'graduationTicket.mahasiswa',
            'graduationTicket.graduationEvent',
            'scannedBy'
        ]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('graduationTicket.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('graduation_event_id')) {
            $query->whereHas('graduationTicket', function ($q) use ($request) {
                $q->where('graduation_event_id', $request->input('graduation_event_id'));
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('scanned_from')) {
            $query->whereDate('scanned_at', '>=', $request->input('scanned_from'));
        }

        if ($request->filled('scanned_until')) {
            $query->whereDate('scanned_at', '<=', $request->input('scanned_until'));
        }

        if ($request->filled('scanned_by')) {
            $query->where('scanned_by', $request->input('scanned_by'));
        }

        $attendances = $query->latest('scanned_at')->paginate(30)->withQueryString();
        $events = GraduationEvent::where('status', '!=', 'completed')->pluck('name', 'id');

        return view('admin.attendance.index', compact('attendances', 'events'));
    }
}