<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Authentications;

use JuniWalk\SSH\Authentication;

class PublicKeyFile implements Authentication
{
	/** @var string */
	private $username;

	/** @var string */
	private $publicKey;

	/** @var string */
	private $privateKey;

	/** @var string */
	private $password;


	/**
	 * @param string  $username
	 * @param string  $privateKey
	 * @param string|null  $publicKey
	 * @param string|null  $password
	 */
	public function __construct(
		string $username,
		string $privateKey,
		string $publicKey = null,
		string $password = null
	) {
		$this->username = $username;
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->password = $password;

		if (is_null($this->publicKey)) {
			$this->publicKey = $privateKey.'.pub';
		}
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
		return ssh2_auth_pubkey_file(
			$session,
			$this->username,
			$this->publicKey,
			$this->privateKey,
			$this->password ?: ''
		);
	}
}
