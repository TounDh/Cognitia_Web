<?php
// src/Service/DiscordService.php

namespace App\Service;

use GuzzleHttp\Client;

class DiscordService
{
    private Client $client;
    private string $botToken;
    private string $guildId;

    public function __construct(string $botToken, string $guildId)
    {
        $this->client = new Client(['base_uri' => 'https://discord.com/api/v10/']);
        $this->botToken = $botToken;
        $this->guildId = $guildId;
    }

    public function createEventChannel(string $eventTitle, int $eventId): ?string
    {
        try {
            // Create a channel for the event
            $channelResponse = $this->client->post("guilds/{$this->guildId}/channels", [
                'headers' => [
                    'Authorization' => "Bot {$this->botToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => "event-{$eventId}-" . strtolower(str_replace(' ', '-', $eventTitle)),
                    'type' => 0, // Text channel
                ],
            ]);

            $channelData = json_decode($channelResponse->getBody()->getContents(), true);
            $channelId = $channelData['id'];

            // Generate an invite link
            $inviteResponse = $this->client->post("channels/{$channelId}/invites", [
                'headers' => [
                    'Authorization' => "Bot {$this->botToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'max_age' => 86400, // 24 hours
                    'max_uses' => 0,    // Unlimited uses
                ],
            ]);

            $inviteData = json_decode($inviteResponse->getBody()->getContents(), true);
            return "https://discord.gg/{$inviteData['code']}";
        } catch (\Exception $e) {
            // For now, return null if something fails
            return null;
        }
    }
}