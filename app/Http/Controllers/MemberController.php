<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\Signature;
use App\Support\ImageOptimizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_if($user->isCentralAdmin(), 403);
        $search = $request->search;

        $members = $this->buildVisibleMembersQuery($user, $search)
            ->latest()
            ->paginate(10);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        // Only staff create members. Admin roles supervise and review instead.
        abort_unless(auth()->user()->canCreateMembers(), 403);

        return view('members.create');
    }

    public function store(Request $request)
    {
        // Member creation stays with staff to preserve the operational split
        // between front-line data entry and admin oversight.
        abort_unless(auth()->user()->canCreateMembers(), 403);

        $request->validate([
            'account_number' => 'required|unique:members',
            'name' => 'required|string|max:255',
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=800,min_height=400,max_width=2000,max_height=1500'
        ], [
            'account_number.unique' => 'That account number is already registered.',
            'signature.image' => 'The signature must be an image file.',
            'signature.mimes' => 'The signature must be a jpeg, png, or jpg file.',
            'signature.max' => 'The signature may not be greater than 1 MB.',
            'signature.dimensions' => 'The signature must be between 800x400 and 2000x1500 pixels for optimal quality and file size.',
        ]);

        $member = Member::create([
            'account_number' => $request->account_number,
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'CREATE_MEMBER',
            'description' => 'Created signature card for ' . $member->account_number
        ]);

        // Signature images are the biggest long-term storage cost in the system,
        // so uploads go through the optimizer before we save the path.
        $path = ImageOptimizer::storeSignature($request->file('signature'));

        Signature::create([
            'member_id' => $member->id,
            'image_path' => $path,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('members.index')->with('success', 'Signature card added successfully');
    }

    public function show(Member $member)
    {
        abort_unless(auth()->user()->canViewMember($member), 403);

        // The detail page needs the signature and ownership context together.
        $member->load(['signature', 'creator.branch']);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'VIEW_MEMBER',
            'description' => 'Viewed member ' . $member->account_number
        ]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        // Branch admins can maintain cards, but only for members in their branch scope.
        abort_unless(auth()->user()->canManageMember($member), 403);

        $member->load(['signature', 'creator.branch']);

        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        abort_unless(auth()->user()->canManageMember($member), 403);

        $request->validate([
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=800,min_height=400,max_width=2000,max_height=1500'
        ], [
            'signature.image' => 'The signature must be an image file.',
            'signature.mimes' => 'The signature must be a jpeg, png, or jpg file.',
            'signature.max' => 'The signature may not be greater than 1 MB.',
            'signature.dimensions' => 'The signature must be between 800x400 and 2000x1500 pixels for optimal quality and file size.',
        ]);

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'UPDATE_MEMBER',
            'description' => 'Updated signature card for ' . $member->account_number
        ]);

        if ($request->hasFile('signature')) {
            $path = ImageOptimizer::storeSignature($request->file('signature'));

            if ($member->signature) {
                // We replace the old image so storage does not keep growing with
                // abandoned card files for the same member.
                $this->deleteSignatureFile($member->signature->image_path);
                $member->signature->update([
                    'image_path' => $path,
                    'created_by' => Auth::id()
                ]);
            } else {
                Signature::create([
                    'member_id' => $member->id,
                    'image_path' => $path,
                    'created_by' => Auth::id()
                ]);
            }
        }

        return redirect()->route('members.index')->with('success', 'Signature card updated successfully');
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isBranchAdmin(), 403);

        $search = $request->get('search');

        // Export reuses the same scoped visibility query as the member list so
        // branch admins cannot export records outside their own branch.
        $members = $this->buildVisibleMembersQuery($user, $search)
            ->latest()
            ->get();

        $filename = 'member-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($members) {
            $handle = fopen('php://output', 'w');

            // Streamed CSV export is lightweight and avoids holding a full report
            // string in memory before sending it to the browser.
            fputcsv($handle, ['Note: When opening this file in Excel or another spreadsheet app, increase the column widths so the data displays cleanly.']);
            fputcsv($handle, ['Account Number', 'Member Name', 'Created By', 'Creator Role', 'Branch', 'Created At']);

            foreach ($members as $member) {
                fputcsv($handle, [
                    $member->account_number,
                    $member->name,
                    $member->creator?->name ?? $member->signature?->creator?->name ?? 'Unknown',
                    $member->creator?->role
                        ? str_replace('_', ' ', $member->creator->role)
                        : ($member->signature?->creator?->role ? str_replace('_', ' ', $member->signature->creator->role) : 'Unknown'),
                    $member->creator?->branch?->name ?? $member->signature?->creator?->branch?->name ?? 'N/A',
                    optional($member->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function confirmDelete(Member $member)
    {
        // Delete is intentionally a separate preview step to reduce wrong-record
        // removal in branch operations.
        abort_unless(auth()->user()->canManageMember($member), 403);

        $member->load(['signature', 'creator.branch']);

        return view('members.delete', compact('member'));
    }

    public function destroy(Member $member)
    {
        abort_unless(auth()->user()->canManageMember($member), 403);

        $request = request();
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        if ($member->signature) {
            // Remove the stored file as part of the delete so disk usage follows
            // the actual number of live member records.
            $this->deleteSignatureFile($member->signature->image_path);
        }

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'DELETE_MEMBER',
            'description' => 'Deleted member ' . $member->account_number,
        ]);

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully');
    }

    public function signature(Member $member)
    {
        abort_unless(auth()->user()->canViewMember($member), 403);
        abort_unless($member->signature, 404);

        $signatureDisk = $this->resolveSignatureDisk($member->signature->image_path);

        if ($signatureDisk) {
            return Storage::disk($signatureDisk)->response($member->signature->image_path);
        }

        abort(404);
    }

    /**
     * Scope member visibility by user role.
     *
     * Central admin is intentionally excluded from member-card access.
     * Branch admins and staff can only see members created inside their own branch.
     */
    private function buildVisibleMembersQuery($user, ?string $search = null): Builder
    {
        $query = Member::with(['creator.branch', 'signature.creator.branch']);

        $query->where(function ($innerQuery) use ($user) {
            $innerQuery->whereHas('creator', function ($creatorQuery) use ($user) {
                $creatorQuery->where('branch_id', $user->branch_id);
            })->orWhereHas('signature.creator', function ($signatureCreatorQuery) use ($user) {
                $signatureCreatorQuery->where('branch_id', $user->branch_id);
            });
        });

        return $query->when($search, function ($innerQuery) use ($search) {
            $innerQuery->where(function ($searchQuery) use ($search) {
                $searchQuery->where('account_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        });
    }

    private function resolveSignatureDisk(string $path): ?string
    {
        $primaryDisk = config('filesystems.signature_cards.disk', 'local');

        if (Storage::disk($primaryDisk)->exists($path)) {
            return $primaryDisk;
        }

        // Legacy public-file fallback is opt-in so production can be locked to
        // private-only access after the migration command has been run.
        if (config('filesystems.signature_cards.allow_public_fallback')
            && Storage::disk('public')->exists($path)) {
            return 'public';
        }

        return null;
    }

    private function deleteSignatureFile(string $path): void
    {
        $signatureDisk = $this->resolveSignatureDisk($path);

        if ($signatureDisk) {
            Storage::disk($signatureDisk)->delete($path);
        }
    }
}
