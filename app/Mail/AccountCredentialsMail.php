<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $employeeName;
    public string $email;
    public ?string $department;
    public ?string $position;
    public string $accountType;
    public string $password;
    public ?string $portalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $employeeName,
        string $email,
        ?string $department,
        ?string $position,
        string $accountType,
        string $password,
        ?string $portalUrl = null
    ) {
        $this->employeeName = $employeeName;
        $this->email = $email;
        $this->department = $department;
        $this->position = $position;
        $this->accountType = $accountType;
        $this->password = $password;
        $defaultPortal = rtrim(config('app.url'), '/') . '/login';
        $this->portalUrl = $portalUrl ?? $defaultPortal;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Your ' . strtoupper($this->accountType) . ' Account Credentials')
            ->view('emails.accounts.credentials');
    }
}
