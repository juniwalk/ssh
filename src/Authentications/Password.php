<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;

class Password implements Authentication
{
	public function __construct(
		private string $username,
		private string $password = ''
	) { }


	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}


	/**
	 * @param  resource  $session
	 * @return bool
	 */
	public function authenticate($session): bool
	{
		// Throws warning on bad authentication
		return @ssh2_auth_password($session, $this->username, $this->password);
	}
}
