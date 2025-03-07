<?php
// src/Command/TestSmsCommand.php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\TwilioService;

#[AsCommand(
    name: 'app:test-sms',
    description: 'Test sending an SMS via Twilio API.',
)]
class TestSmsCommand extends Command
{
    private TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('phone_number', InputArgument::REQUIRED, 'The phone number to send the SMS to.')
            ->addArgument('message', InputArgument::OPTIONAL, 'The message to send.', 'Hello, this is a test SMS!');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phoneNumber = $input->getArgument('phone_number');
        $message = $input->getArgument('message');

        // Envoyer le SMS via le service Twilio
        $this->twilioService->sendSms($phoneNumber, $message);

        $output->writeln(sprintf('SMS sent to %s: %s', $phoneNumber, $message));

        return Command::SUCCESS;
    }
}