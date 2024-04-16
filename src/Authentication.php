<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH;

use FTP\Connection;
use JuniWalk\SSH\Exceptions\AuthenticationException;

interface Authentication
{
	public function getUsername(): string;

	/**
	 * @param  resource $session
	 * @throws AuthenticationException
	 */
	public function authenticate($session): bool;


	/**
	 * @throws AuthenticationException
	 */
	public function login(Connection $session): bool;
}
