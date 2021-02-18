<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;

class HostBasedFile implements Authentication
{
	/** @var string */
	private $username;

	/** @var string */
	private $hostname;

	/** @var string */
	private $publicKey;

	/** @var string */
	private $privateKey;

	/** @var string */
	private $password;

	/** @var string */
	private $localUser;


	/**
	 * @param string  $username
	 * @param string  $hostname
	 * @param string  $publicKey
	 * @param string  $privateKey
	 * @param string  $password
	 * @param string|null  $localUser
	 */
	public function __construct(
		string $username,
		string $hostname,
		string $publicKey,
		string $privateKey,
		string $password = '',
		string $localUser = null
	) {
		$this->username = $username;
		$this->hostname = $hostname;
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->password = $password;
		$this->localUser = $localUser;
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
		return ssh2_auth_hostbased_file(
			$session,
			$this->username,
			$this->hostname,
			$this->publicKey,
			$this->privateKey,
			$this->password,
			$this->localUser
		);
	}
}
