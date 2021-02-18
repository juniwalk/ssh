<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

class Password implements Authentication
{
	/** @var string */
	private $username;

	/** @var string */
	private $password;


	/**
	 * @param string  $username
	 * @param string  $password
	 */
	public function __construct(string $username, string $password = '')
	{
		$this->username = $username;
		$this->password = $password;
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
	 */
	public function authenticate($session): bool
	{
		// Throws warning on bad authentication
		return @ssh2_auth_password($session, $this->username, $this->password);
	}
}
