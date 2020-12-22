<?php

declare(strict_types=1);

namespace IA\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

interface CookieJarInterface
{
    /**
     * @return Cookie[]
     */
    public function all(): array;

    /**
     * @param string $name
     * @param string|null $path
     * @param Cookie|array|null $default
     * @return Cookie|array|null
     */
    public function get(string $name, ?string $path = null, Cookie|array|null $default = null): Cookie|array|null;

    /**
     * @param string $name
     * @param string|null $path
     * @return bool
     */
    public function has(string $name, ?string $path = null): bool;

    /**
     * @param Cookie|mixed ...$parameters
     */
    public function add(...$parameters): void;

    /**
     * @param string $name
     * @param string|null $path
     */
    public function remove(string $name, ?string $path = null): void;

    /**
     * @param string $name
     * @param string|null $value
     * @param int $expire
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param string|null $sameSite
     * @return Cookie
     */
    public function create(
        string $name,
        ?string $value,
        int $expire = 0,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        bool $httpOnly = true,
        bool $raw = false,
        ?string $sameSite = null
    ): Cookie;

    /**
     * @param string $name
     * @param string|null $value
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param string|null $sameSite
     * @return Cookie
     */
    public function forever(
        string $name,
        ?string $value,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        bool $httpOnly = true,
        bool $raw = false,
        ?string $sameSite = null
    ): Cookie;

    /**
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @return Cookie
     */
    public function forget(string $name, ?string $path = null, ?string $domain = null): Cookie;
}