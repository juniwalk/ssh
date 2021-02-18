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
	/** @var string */
	private $username;


	/**
	 * @param string  $username
	 */
	public function __construct(string $username)
	{
		$this->username = $username;
	}


	/**
	 * @return string
	 */
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
