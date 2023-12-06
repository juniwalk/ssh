<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;

class PublicKeyFile implements Authentication
{
	public function __construct(
		private readonly string $username,
		private readonly string $privateKey,
		private readonly string $publicKey = null,
		private readonly string $password = null
	) {
		if (is_null($publicKey)) {
			$this->publicKey = $privateKey.'.pub';
		}
	}


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
		return ssh2_auth_pubkey_file(
			$session,
			$this->username,
			$this->publicKey,
			$this->privateKey,
			$this->password ?: ''
		);
	}
}
