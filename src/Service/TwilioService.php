<?php
// src/Service/TwilioService.php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private string $accountSid;
    private string $authToken;
    private string $fromNumber;

    public function __construct(string $accountSid, string $authToken, string $fromNumber)
    {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->fromNumber = $fromNumber;
    }

    public function sendSms(string $to, string $message): void
    {
        $client = new Client($this->accountSid, $this->authToken);

        $client->messages->create(
            $to,
            [
                'from' => $this->fromNumber,
                'body' => $message,
            ]
        );
    }
}