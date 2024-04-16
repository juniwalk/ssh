<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use FTP\Connection;
use JuniWalk\SSH\Authentication;
use JuniWalk\SSH\Exceptions\AuthenticationException;
use SensitiveParameter;

class PublicKeyFile implements Authentication
{
	public function __construct(
		private string $username,
		private string $privateKey,
		private string $publicKey = '',
		#[SensitiveParameter]
		private string $password = '',
	) {
		if (empty($publicKey)) {
			$this->publicKey = $privateKey.'.pub';
		}
	}


	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * @param resource $session
	 */
	public function authenticate($session): bool
	{
		return ssh2_auth_pubkey_file(
			$session,
			$this->username,
			$this->publicKey,
			$this->privateKey,
			$this->password,
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
