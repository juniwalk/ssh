<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;
use JuniWalk\SSH\Exceptions\AuthenticationException;

class Agent implements Authentication
{
	public function __construct(
		private string $username,
	) { }


	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * @param  resource  $session
	 * @return bool
	 * @throws AuthenticationException
	 */
	public function authenticate($session): bool
	{
		if (!function_exists('ssh2_auth_agent')) {
			throw AuthenticationException::fromAuth($this, 'Method not available');
		}

		return ssh2_auth_agent($session, $this->username);
	}
}
