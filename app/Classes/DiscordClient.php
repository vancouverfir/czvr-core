<?php

namespace App\Classes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * In-house discord client cause no good libraries exist...
 *
 * @author kolbyd
 */
class DiscordClient
{
    private Client $http;
    private string $token;
    private const AUDIT_CHANNEL = 1219713888636108921;
    private const VANCOUVER_GUILD = 589477926961938443;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->http = new Client([
            'base_uri' => 'https://discord.com/api/v10/',
            'headers' => [
                'Authorization' => 'Bot '.$this->token,
                'User-Agent' => 'DiscordBot (czvr.ca, v1.0.0)',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function AddGuildMember(int $user_id, string $access_token, string $nickname, array $roles): void
    {
        try {
            $this->http->put('guilds/'.self::VANCOUVER_GUILD."/members/$user_id", [
                'json' => [
                    'access_token' => $access_token,
                    'nick' => $nickname,
                    'roles' => null,
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }

    public function GetGuildMember(int $user_id): ?object
    {
        try {
            $response = $this->http->get('guilds/'.self::VANCOUVER_GUILD."/members/$user_id");
            if ($response->getStatusCode() != 200) {
                return null;
            }

            return json_decode($response->getBody());
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public function RemoveGuildMember(int $user_id): void
    {
        try {
            $this->http->delete('guilds/'.self::VANCOUVER_GUILD."/members/$user_id", [
                'headers' => [
                    'X-Audit-Log-Reason' => 'User disconnected via website.',
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }

    public function GetDiscordUser(int $user_id): ?object
    {
        try {
            $response = $this->http->get("users/$user_id");
            if ($response->getStatusCode() !== 200) {
                throw new Exception("Couldn't get user $user_id.");
            }

            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            Log::error($e->getMessage());
        }

        // No user found
        return null;
    }

    public function SendAuditMessage(string $content): void
    {
        try {
            $this->http->post('channels/'.self::AUDIT_CHANNEL.'/messages', [
                'json' => ['content' => $content],
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }
}
