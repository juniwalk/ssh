<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

namespace JuniWalk\SSH;

use JuniWalk\SSH\Exceptions\AuthenticationException;
use JuniWalk\SSH\Exceptions\ConnectionException;

interface Service
{
	public function getHost(): ?string;
	public function getPort(): int;
	public function isConnected(): bool;

	/**
	 * @throws AuthenticationException
	 * @throws ConnectionException
	 */
	public function connect(string $host, int $port, Authentication $auth): static;

	public function disconnect(): void;
}
