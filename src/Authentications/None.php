<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;
use JuniWalk\SSH\Exceptions\AuthenticationException;

class None implements Authentication
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
		$result = ssh2_auth_none($session, $this->username);

		if (is_bool($result)) {
			return $result;
		}

		throw AuthenticationException::fromAuth($this, 'Method not available, use one of '.implode(', ', $result));
	}
}
