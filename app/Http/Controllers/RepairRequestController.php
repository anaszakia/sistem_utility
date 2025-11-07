<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\RepairSchedule;
use App\Models\TechnicianAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepairRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RepairRequest::with(['reporter', 'approver', 'assignments.technician']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('facility_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $user = auth()->user();
        if ($user->hasRole('admin_departemen')) {
            // Admin departemen hanya bisa lihat laporan dari departemennya
            $query->where('reported_by', $user->id);
        } elseif ($user->hasRole('teknisi_utility')) {
            // Teknisi hanya bisa lihat yang assigned ke dia
            $query->whereHas('assignments', function($q) use ($user) {
                $q->where('technician_id', $user->id);
            });
        }

        $repairRequests = $query->latest()->paginate(15);

        return view('repair-requests.index', compact('repairRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('repair-requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'facility_type' => 'required|string|max:255',
            'facility_name' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048', // Max 2MB per image
        ]);

        DB::beginTransaction();
        try {
            // Handle image uploads
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('repair-requests', 'public');
                    $imagePaths[] = $path;
                }
            }

            $repairRequest = RepairRequest::create([
                'reported_by' => auth()->id(),
                'department' => $validated['department'],
                'location' => $validated['location'],
                'facility_type' => $validated['facility_type'],
                'facility_name' => $validated['facility_name'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'notes' => $validated['notes'] ?? null,
                'images' => $imagePaths,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('repair-requests.show', $repairRequest)
                ->with('success', 'Laporan kerusakan berhasil dibuat dengan nomor: ' . $repairRequest->request_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairRequest $repairRequest)
    {
        $repairRequest->load(['reporter', 'approver', 'schedule.creator', 'assignments.technician', 'assignments.assignedBy']);

        return view('repair-requests.show', compact('repairRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RepairRequest $repairRequest)
    {
        // Hanya bisa edit jika status masih pending
        if (!$repairRequest->isPending()) {
            return back()->with('error', 'Tidak dapat mengedit laporan yang sudah diproses.');
        }

        return view('repair-requests.edit', compact('repairRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RepairRequest $repairRequest)
    {
        // Hanya bisa update jika status masih pending
        if (!$repairRequest->isPending()) {
            return back()->with('error', 'Tidak dapat mengupdate laporan yang sudah diproses.');
        }

        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'facility_type' => 'required|string|max:255',
            'facility_name' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle new image uploads
            $imagePaths = $repairRequest->images ?? [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('repair-requests', 'public');
                    $imagePaths[] = $path;
                }
            }

            $repairRequest->update([
                'department' => $validated['department'],
                'location' => $validated['location'],
                'facility_type' => $validated['facility_type'],
                'facility_name' => $validated['facility_name'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'notes' => $validated['notes'] ?? null,
                'images' => $imagePaths,
            ]);

            DB::commit();

            return redirect()->route('repair-requests.show', $repairRequest)
                ->with('success', 'Laporan kerusakan berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate laporan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairRequest $repairRequest)
    {
        // Hanya bisa delete jika status pending atau rejected
        if (!in_array($repairRequest->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Tidak dapat menghapus laporan yang sedang diproses.');
        }

        // Delete images
        if ($repairRequest->images) {
            foreach ($repairRequest->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $repairRequest->delete();

        return redirect()->route('repair-requests.index')
            ->with('success', 'Laporan kerusakan berhasil dihapus.');
    }

    /**
     * Approve repair request and create schedule
     */
    public function approve(Request $request, RepairRequest $repairRequest)
    {
        if (!$repairRequest->isPending()) {
            return back()->with('error', 'Laporan sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'schedule_description' => 'nullable|string',
            'approval_notes' => 'nullable|string',
            'technician_ids' => 'required|array|min:1',
            'technician_ids.*' => 'exists:users,id',
            'assignment_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update repair request status
            $repairRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // Create schedule
            $schedule = RepairSchedule::create([
                'repair_request_id' => $repairRequest->id,
                'scheduled_start' => $validated['scheduled_start'],
                'scheduled_end' => $validated['scheduled_end'],
                'description' => $validated['schedule_description'] ?? null,
                'status' => 'scheduled',
                'created_by' => auth()->id(),
            ]);

            // Assign technicians
            foreach ($validated['technician_ids'] as $technicianId) {
                TechnicianAssignment::create([
                    'repair_request_id' => $repairRequest->id,
                    'technician_id' => $technicianId,
                    'assigned_by' => auth()->id(),
                    'status' => 'assigned',
                    'notes' => $validated['assignment_notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('repair-requests.show', $repairRequest)
                ->with('success', 'Laporan berhasil di-approve dan teknisi telah ditugaskan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal approve laporan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Reject repair request
     */
    public function reject(Request $request, RepairRequest $repairRequest)
    {
        if (!$repairRequest->isPending()) {
            return back()->with('error', 'Laporan sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $repairRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()->route('repair-requests.show', $repairRequest)
            ->with('success', 'Laporan telah ditolak.');
    }

    /**
     * Complete repair request - Admin utility marks as completed
     */
    public function complete(Request $request, RepairRequest $repairRequest)
    {
        // Only allow completion if status is approved or in_progress
        if (!in_array($repairRequest->status, ['approved', 'in_progress'])) {
            return back()->with('error', 'Hanya laporan dengan status Approved atau In Progress yang bisa diselesaikan.');
        }

        $validated = $request->validate([
            'completion_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update repair request to completed
            $repairRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_notes' => $validated['completion_notes'] ?? null,
            ]);

            // Update schedule status if exists
            if ($repairRequest->schedule) {
                $repairRequest->schedule->update([
                    'status' => 'completed',
                    'actual_end' => now(),
                ]);
            }

            // Mark all technician assignments as completed
            // This will make technicians available again
            $repairRequest->assignments()->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('repair-requests.show', $repairRequest)
                ->with('success', 'Laporan telah diselesaikan. Teknisi sekarang tersedia untuk tugas baru.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Mark repair request as in progress
     */
    public function startProgress(RepairRequest $repairRequest)
    {
        if (!$repairRequest->isApproved()) {
            return back()->with('error', 'Hanya laporan yang sudah di-approve yang bisa dimulai.');
        }

        DB::beginTransaction();
        try {
            // Update repair request status
            $repairRequest->update([
                'status' => 'in_progress',
            ]);

            // Update schedule status if exists
            if ($repairRequest->schedule) {
                $repairRequest->schedule->update([
                    'status' => 'in_progress',
                    'actual_start' => now(),
                ]);
            }

            // Update assignments status
            $repairRequest->assignments()->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('repair-requests.show', $repairRequest)
                ->with('success', 'Pekerjaan perbaikan telah dimulai.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai pekerjaan: ' . $e->getMessage());
        }
    }

    /**
     * Get available technicians (teknisi yang tidak sedang ditugaskan)
     */
    public function getAvailableTechnicians()
    {
        $technicians = User::role('teknisi_utility')
            ->whereDoesntHave('assignedTasks', function($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            })
            ->get(['id', 'name', 'email']);

        return response()->json($technicians);
    }
}
