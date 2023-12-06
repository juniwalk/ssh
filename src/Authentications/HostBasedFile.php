<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;

class HostBasedFile implements Authentication
{
	public function __construct(
		private readonly string $username,
		private readonly string $hostname,
		private readonly string $publicKey,
		private readonly string $privateKey,
		private readonly string $password = '',
		private readonly string $localUser = null,
	) { }


	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * @param  resource  $session
	 * @return bool
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
			$this->localUser
		);
	}
}
