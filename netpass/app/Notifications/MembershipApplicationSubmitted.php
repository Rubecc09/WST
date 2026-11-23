<?php

namespace App\Notifications;

use App\Models\MembershipTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Cache;

class MembershipApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public string $memberName;

    public string $levelName;

    public string $referenceNumber;

    public function __construct(MembershipTransaction $transaction)
    {
        $this->memberName = $transaction->member->full_name;
        $this->levelName = $transaction->level_name;
        $this->referenceNumber = $transaction->reference_number;
        $this->onConnection('database');
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    private function deliveryKey(): string
    {
        return 'membership-application-email:'.$this->referenceNumber;
    }

    public function middleware(object $notifiable, string $channel): array
    {
        return [(new WithoutOverlapping($this->deliveryKey()))->releaseAfter(30)->expireAfter(180)];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return ! Cache::has($this->deliveryKey());
    }

    public function afterSending(object $notifiable, string $channel, mixed $response): void
    {
        Cache::forever($this->deliveryKey(), true);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('NetPass membership application received')
            ->view(['html' => 'emails.membership-application', 'text' => 'emails.membership-application-text'])
            ->greeting('Hello '.$this->memberName.',')
            ->line('Your membership application has been received.')
            ->line('Member: '.$this->memberName)
            ->line('Membership level: '.$this->levelName)
            ->line('Reference number: '.$this->referenceNumber)
            ->line('Application status at submission: Pending')
            ->line('Membership becomes effective only after payment and approval.');
    }
}
