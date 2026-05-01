<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Member;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logs_render_after_actor_is_deleted(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'location' => 'Downtown',
        ]);

        $centralAdmin = User::factory()->create([
            'role' => 'central_admin',
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $branch->id,
        ]);

        ActivityLog::create([
            'user_id' => $staff->id,
            'action' => 'VIEW_MEMBER',
            'description' => 'Viewed member 10001',
        ]);

        $staff->delete();

        $response = $this->actingAs($centralAdmin)->get(route('admin.activity-logs'));

        $response->assertOk();
        $response->assertSee('Deleted account');
        $response->assertSee('Main Branch');
    }

    public function test_disabling_staff_blocks_login_without_removing_member_card_ownership(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'location' => 'Downtown',
        ]);

        $branchAdmin = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $branch->id,
            'created_by' => $branchAdmin->id,
        ]);

        $member = Member::create([
            'account_number' => '10001',
            'name' => 'Jane Member',
            'created_by' => $staff->id,
        ]);

        $signature = Signature::create([
            'member_id' => $member->id,
            'image_path' => 'signatures/example.jpg',
            'created_by' => $staff->id,
        ]);

        $response = $this->actingAs($branchAdmin)->patch(route('admin.users.status.update', $staff), [
            'action' => 'disable',
            'current_password' => 'password',
            'status_reason' => 'Annual leave',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'status' => 'disabled',
        ]);
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'created_by' => $staff->id,
        ]);
        $this->assertDatabaseHas('signatures', [
            'id' => $signature->id,
            'created_by' => $staff->id,
        ]);

        $this->actingAs($branchAdmin)
            ->get(route('members.show', $member->fresh()))
            ->assertOk()
            ->assertSee('Jane Member');

        auth()->logout();

        $this->post('/login', [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    public function test_branch_admin_can_reactivate_staff_after_leave(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'location' => 'Downtown',
        ]);

        $branchAdmin = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $branch->id,
            'created_by' => $branchAdmin->id,
            'status' => 'disabled',
            'status_reason' => 'Annual leave',
        ]);

        $this->actingAs($branchAdmin)->patch(route('admin.users.status.update', $staff), [
            'action' => 'enable',
            'current_password' => 'password',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_reset_issues_temporary_password_and_forces_change(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'location' => 'Downtown',
        ]);

        $branchAdmin = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $branch->id,
            'created_by' => $branchAdmin->id,
        ]);

        $response = $this->actingAs($branchAdmin)->put(route('admin.users.password.update', $staff), [
            'current_password' => 'password',
        ]);

        $response->assertRedirect(route('admin.users.password.confirm', $staff));
        $response->assertSessionHas('temporary_password');

        $temporaryPassword = $response->getSession()->get('temporary_password');

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'must_change_password' => true,
        ]);

        auth()->logout();

        $this->post('/login', [
            'email' => $staff->email,
            'password' => $temporaryPassword,
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('dashboard'))->assertRedirect(route('password.change.edit'));

        $this->put(route('password.change.update'), [
            'current_password' => $temporaryPassword,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'must_change_password' => false,
        ]);
    }

    public function test_central_admin_can_reset_branch_admin_with_temporary_password(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'location' => 'Downtown',
        ]);

        $centralAdmin = User::factory()->create([
            'role' => 'central_admin',
        ]);

        $branchAdmin = User::factory()->create([
            'role' => 'admin',
            'branch_id' => $branch->id,
            'created_by' => $centralAdmin->id,
        ]);

        $response = $this->actingAs($centralAdmin)->put(route('admin.users.password.update', $branchAdmin), [
            'current_password' => 'password',
        ]);

        $response->assertRedirect(route('admin.users.password.confirm', $branchAdmin));
        $response->assertSessionHas('temporary_password');
        $this->assertDatabaseHas('users', [
            'id' => $branchAdmin->id,
            'must_change_password' => true,
        ]);
    }
}
