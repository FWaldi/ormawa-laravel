<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    public function index()
    {
        try {
            $organizations = Organization::with('members')->paginate(10);
            $faculties = Organization::distinct('faculty')->pluck('faculty')->filter()->sort()->values();
        } catch (\Exception $e) {
            // Fallback to empty collections if database is not available
            $organizations = collect();
            $faculties = collect();
        }
        return view('organizations.index', compact('organizations', 'faculties'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'contact' => 'nullable|string|max:255',
            'social_media' => 'nullable|array',
            'social_media.facebook' => 'nullable|url',
            'social_media.twitter' => 'nullable|url',
            'social_media.instagram' => 'nullable|url',
            'social_media.linkedin' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $organization = Organization::create($validated);

        // Audit log for organization creation
        Log::info('Organization created', [
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => $request->ip(),
            'action' => 'organization_created'
        ]);

        return redirect()->route('organizations.show', $organization->id)
            ->with('success', 'Organization created successfully.');
    }

    public function show($id)
    {
        $organization = Organization::with('members', 'activities', 'news')->findOrFail($id);
        return view('organizations.show', compact('organization'));
    }

    public function edit($id)
    {
        $organization = Organization::findOrFail($id);
        
        // Check if user is org_admin or admin
        if (!Auth::user()->isAdmin() && !Auth::user()->isOrgAdminForOrganization($organization->id)) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);
        
        // Check if user is org_admin or admin
        if (!Auth::user()->isAdmin() && !Auth::user()->isOrgAdminForOrganization($organization->id)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('organizations')->ignore($organization->id)],
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'contact' => 'nullable|string|max:255',
            'social_media' => 'nullable|array',
            'social_media.facebook' => 'nullable|url',
            'social_media.twitter' => 'nullable|url',
            'social_media.instagram' => 'nullable|url',
            'social_media.linkedin' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $organization->update($validated);

        // Audit log for organization update
        Log::info('Organization updated', [
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => $request->ip(),
            'action' => 'organization_updated',
            'changes' => $organization->getChanges()
        ]);

        return redirect()->route('organizations.show', $organization->id)
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        
        // Only admins can delete organizations
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete the organization (logo and data preserved)
        $organization->delete();

        // Audit log for organization deletion (soft delete)
        Log::warning('Organization soft deleted', [
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => request()->ip(),
            'action' => 'organization_soft_deleted'
        ]);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    public function addMember(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);
        
        // Check if user is org_admin or admin
        if (!Auth::user()->isAdmin() && !Auth::user()->isOrgAdminForOrganization($organization->id)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,org_admin',
        ]);

        $user = User::findOrFail($validated['user_id']);
        
        // Check if user is already a member
        if ($user->organization_id === $organization->id) {
            return redirect()->back()
                ->with('error', 'User is already a member of this organization.');
        }

        $user->update([
            'organization_id' => $organization->id,
            'role' => $validated['role'],
        ]);

        // Audit log for member addition
        Log::info('Member added to organization', [
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'assigned_role' => $validated['role'],
            'ip_address' => $request->ip(),
            'action' => 'member_added'
        ]);

        return redirect()->route('organizations.show', $organization->id)
            ->with('success', 'Member added successfully.');
    }

    public function removeMember($id, $userId)
    {
        $organization = Organization::findOrFail($id);
        
        // Check if user is org_admin or admin
        if (!Auth::user()->isAdmin() && !Auth::user()->isOrgAdminForOrganization($organization->id)) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($userId);
        
        // Don't allow removing the last admin
        if ($user->role === 'org_admin') {
            $adminCount = $organization->members()->where('role', 'org_admin')->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Cannot remove the last organization admin.');
            }
        }

        $previousRole = $user->role;
        
        $user->update([
            'organization_id' => null,
            'role' => 'member',
        ]);

        // Audit log for member removal
        Log::info('Member removed from organization', [
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'previous_role' => $previousRole,
            'ip_address' => $request->ip(),
            'action' => 'member_removed'
        ]);

        return redirect()->route('organizations.show', $organization->id)
            ->with('success', 'Member removed successfully.');
    }
}