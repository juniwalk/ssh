<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use FTP\Connection;
use JuniWalk\SSH\Authentication;
use SensitiveParameter;

class HostBasedFile implements Authentication
{
	public function __construct(
		private string $username,
		private string $hostname,
		private string $publicKey,
		private string $privateKey,
		#[SensitiveParameter]
		private string $password = '',
		private string $localUser = null,
	) {
	}


	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * @param  resource $session
	 */
	public function authenticate($session): bool
	{
		return ssh2_auth_hostbased_file(
			$session,
			$this->username,
			$this->hostname,
			$this->publicKey,
			$this->privateKey,
			$this->password,
			$this->localUser,
		);
	}


	/**
	 * @throws AuthenticationException
	 */
	public function login(Connection $session): bool
	{
		throw AuthenticationException::fromAuth($this, 'Method not available.');
	}
}
