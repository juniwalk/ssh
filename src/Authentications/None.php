<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use FTP\Connection;
use JuniWalk\SSH\Authentication;
use JuniWalk\SSH\Exceptions\AuthenticationException;

class None implements Authentication
{
	public function __construct(
		private string $username,
	) {
	}


	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * @param  resource $session
	 * @throws AuthenticationException
	 */
	public function authenticate($session): bool
	{
		$result = ssh2_auth_none($session, $this->username);

		if (is_bool($result)) {
			return $result;
		}

		throw AuthenticationException::fromAuth($this, 'Method not available, use one of '.implode(', ', $result).'.');
	}


	/**
	 * @throws AuthenticationException
	 */
	public function login(Connection $session): bool
	{
		// Throws warning on bad authentication
		return @ftp_login($session, $this->username, '');
	}
}
