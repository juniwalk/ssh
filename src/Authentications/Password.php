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

class Password implements Authentication
{
	public function __construct(
		private string $username,
		#[SensitiveParameter]
		private string $password = '',
	) {
	}


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


	/**
	 * @throws AuthenticationException
	 */
	public function login(Connection $session): bool
	{
		// Throws warning on bad authentication
		return @ftp_login($session, $this->username, $this->password);
	}
}
