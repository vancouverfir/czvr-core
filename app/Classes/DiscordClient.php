<?php

namespace App\Classes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * In-house discord client cause no good libraries exist...
 *
 * @author kolbyd
 */
class DiscordClient
{
    private PendingRequest $http;

    private string $token;

    private const AUDIT_CHANNEL = 1219713888636108921;

    private const VANCOUVER_GUILD = 589477926961938443;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->http = Http::baseUrl('https://discord.com/api/v10')
            ->withToken($this->token, 'Bot')
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'DiscordBot (czvr.ca, v1.1.1)'])
            ->connectTimeout(5)
            ->timeout(15);
    }

    public function AddGuildMember(int $user_id, string $access_token, string $nickname, array $roles): bool
    {
        try {
            $response = $this->http->put('guilds/'.self::VANCOUVER_GUILD."/members/$user_id", [
                'access_token' => $access_token,
                'nick' => $nickname,
                'roles' => $roles,
            ]);

            if (! $response->successful()) {
                Log::warning('Discord guild member add failed.', ['status' => $response->status()]);
            }

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::warning('Discord guild member add could not connect!', ['message' => $e->getMessage()]);

            return false;
        }
    }

    public function GetGuildMember(int $user_id): ?object
    {
        try {
            $response = $this->http->get('guilds/'.self::VANCOUVER_GUILD."/members/$user_id");
            if (! $response->successful()) {
                return null;
            }

            return $response->object();
        } catch (ConnectionException $e) {
            Log::warning('Discord guild member lookup could not connect!', ['message' => $e->getMessage()]);
        }

        return null;
    }

    public function RemoveGuildMember(int $user_id): void
    {
        try {
            $response = $this->http
                ->withHeaders(['X-Audit-Log-Reason' => 'User disconnected via website!'])
                ->delete('guilds/'.self::VANCOUVER_GUILD."/members/$user_id");

            if (! $response->successful()) {
                Log::warning('Discord guild member removal failed!', ['status' => $response->status()]);
            }
        } catch (ConnectionException $e) {
            Log::warning('Discord guild member removal could not connect!', ['message' => $e->getMessage()]);
        }
    }

    public function GetDiscordUser(int $user_id): ?object
    {
        try {
            $response = $this->http->get("users/$user_id");
            if (! $response->successful()) {
                return null;
            }

            return $response->object();
        } catch (ConnectionException $e) {
            Log::warning('Discord user lookup could not connect!', ['message' => $e->getMessage()]);
        }

        // No user found
        return null;
    }

    public function SendAuditMessage(string $content): void
    {
        try {
            $response = $this->http->post('channels/'.self::AUDIT_CHANNEL.'/messages', [
                'content' => $content,
            ]);

            if (! $response->successful()) {
                Log::warning('Discord audit message failed!', ['status' => $response->status()]);
            }
        } catch (ConnectionException $e) {
            Log::warning('Discord audit message could not connect!', ['message' => $e->getMessage()]);
        }
    }
}
