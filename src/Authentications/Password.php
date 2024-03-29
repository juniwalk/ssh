<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;
use SensitiveParameter;

class Password implements Authentication
{
	public function __construct(
		private string $username,
		#[SensitiveParameter]
		private string $password = ''
	) { }


	public function getUsername(): string
	{
		return $this->username;
	}


	public function getPassword(): string
	{
		return $this->password;
	}


	/**
	 * @param resource $session
	 */
	public function authenticate($session): bool
	{
		// Throws warning on bad authentication
		return @ssh2_auth_password($session, $this->username, $this->password);
	}
}
