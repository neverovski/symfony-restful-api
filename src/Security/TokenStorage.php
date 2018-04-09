<?php

namespace App\Security;


use Predis\Client;

class TokenStorage
{
    const KEY_SUFFIX = '-token';
    /**
     * @var Client
     */
    private $redisClient;

    /**
     * TokenStorage constructor.
     * @param Client $redisClient
     */
    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param string $username
     * @param string $token
     */
    public function storeToken(string $username, string $token): void
    {
        $this->redisClient->set(
            $username.self::KEY_SUFFIX,
            $token
        );
        $this->redisClient->expire(
            $username.self::KEY_SUFFIX,
            3600
        );
    }

    /**
     * @param string $username
     */
    public function invalidateToken(string $username): void
    {
        $this->redisClient->del($username.self::KEY_SUFFIX);
    }

    /**
     * @param string $username
     * @param string $token
     * @return bool
     */
    public function isTokenValid(string $username, string $token): bool
    {
        return $this->redisClient->get($username.self::KEY_SUFFIX) === $token;
    }
}