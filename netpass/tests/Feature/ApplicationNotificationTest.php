<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use App\Notifications\MembershipApplicationSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_dispatches_once_and_invalid_or_duplicate_requests_do_not_dispatch(): void
    {
        Notification::fake();
        $this->actingAs(User::factory()->admin()->create());
        $member = Member::factory()->create();
        $level = MembershipLevel::factory()->create(['fee' => 100, 'validity_days' => 30, 'benefits' => 'Access']);
        $payload = ['member_id' => $member->id, 'membership_level_id' => $level->id];
        $this->post(route('transactions.applications.store'), [])->assertSessionHasErrors();
        Notification::assertNothingSent();
        $this->post(route('transactions.applications.store'), $payload)->assertSessionHasNoErrors();
        $transaction = MembershipTransaction::firstOrFail();
        Notification::assertSentOnDemand(MembershipApplicationSubmitted::class, function ($notification, $channels, $notifiable) use ($member, $transaction): bool {
            return $notifiable->routes['mail'] === $member->email
                && $notification->referenceNumber === $transaction->reference_number
                && $notification->afterCommit === true && $notification->connection === 'database';
        });
        $this->post(route('transactions.applications.store'), $payload)->assertSessionHasErrors('member_id');
        Notification::assertCount(1);
        MembershipTransaction::whereKey($transaction->id)->update(['status' => MembershipTransaction::APPROVED]);
        $this->post(route('transactions.renewals.store'), $payload)->assertSessionHasNoErrors();
        Notification::assertCount(1);
    }

    public function test_email_content_and_replayed_delivery_are_deduplicated(): void
    {
        config(['mail.default' => 'array']);
        $transaction = MembershipTransaction::factory()->create();
        $notification = new MembershipApplicationSubmitted($transaction);
        $recipient = (new AnonymousNotifiable)->route('mail', $transaction->member->email);
        $message = $notification->toMail($recipient);
        $content = implode(' ', $message->introLines);
        $this->assertStringContainsString($transaction->member->full_name, $content);
        $this->assertStringContainsString($transaction->level_name, $content);
        $this->assertStringContainsString($transaction->reference_number, $content);
        $this->assertStringContainsString('Pending', $content);
        Notification::sendNow($recipient, $notification);
        Notification::sendNow($recipient, new MembershipApplicationSubmitted($transaction));
        $this->assertCount(1, Mail::mailer('array')->getSymfonyTransport()->messages());
    }
}
