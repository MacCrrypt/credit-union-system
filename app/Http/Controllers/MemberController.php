<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $members = Member::when($search, function ($query) use ($search) {
            $query->where('account_number', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        })->latest()->paginate(10);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_number' => 'required|unique:members',
            'name' => 'required',
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=800,min_height=400,max_width=2000,max_height=1500'
        ], [
            'account_number.unique' => 'That account number is already registered.',
            'signature.image' => 'The signature must be an image file.',
            'signature.mimes' => 'The signature must be a jpeg, png, or jpg file.',
            'signature.max' => 'The signature may not be greater than 1 MB.',
            'signature.dimensions' => 'The signature must be between 800x400 and 2000x1500 pixels for optimal quality and file size.',
        ]);

        $member = Member::create($request->only('account_number', 'name'));

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'CREATE_MEMBER',
            'description' => 'Created signature card for ' . $member->account_number
        ]);

        $path = $request->file('signature')->store('signatures', 'public');

        Signature::create([
            'member_id' => $member->id,
            'image_path' => $path,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('members.index')->with('success', 'Signature card added successfully');
    }

    public function show(Member $member)
    {
        $member->load('signature');

        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'action' => 'VIEW_MEMBER',
            'description' => 'Viewed member ' . $member->account_number
        ]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $member->load('signature');
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
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
            $path = $request->file('signature')->store('signatures', 'public');

            if ($member->signature) {
                Storage::disk('public')->delete($member->signature->image_path);
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

   
}
