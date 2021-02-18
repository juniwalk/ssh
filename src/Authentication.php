<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH;

use JuniWalk\SSH\Exceptions\AuthenticationException;

interface Authentication
{
	/**
	 * @return string
	 */
	public function getUsername(): string;


	/**
	 * @param  resource  $session
	 * @return bool
	 * @throws AuthenticationException
	 */
	public function authenticate($session): bool;
}
