<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicationLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_map_and_coordinates_are_saved_and_validated(): void
    {
        Notification::fake();
        $this->withoutVite();
        $this->actingAs(User::factory()->admin()->create());
        config(['services.google_maps.key' => 'test-key']);
        $this->get(route('transactions.applications.create'))->assertOk()->assertSee('application-map')->assertSee('14.5995')->assertSee('120.9842')->assertSee('test-key');
        $this->get(route('transactions.renewals.create'))->assertOk()->assertDontSee('application-map');
        $payload = ['member_id' => Member::factory()->create()->id, 'membership_level_id' => MembershipLevel::factory()->create(['fee' => 100, 'validity_days' => 30, 'benefits' => 'Access'])->id];
        foreach ([['latitude' => 91, 'longitude' => 0], ['latitude' => 0, 'longitude' => 181], ['latitude' => 'bad', 'longitude' => 0], ['latitude' => 14.5]] as $invalid) {
            $this->post(route('transactions.applications.store'), $payload + $invalid)->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('membership_transactions', 0);
        $this->post(route('transactions.applications.store'), $payload + ['latitude' => 14.5995, 'longitude' => 120.9842])->assertSessionHasNoErrors();
        $transaction = MembershipTransaction::firstOrFail();
        $this->assertSame('14.5995000', $transaction->latitude);
        $this->assertSame('120.9842000', $transaction->longitude);
        $this->assertSame('Pending', $transaction->status);
    }
}
