<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Exceptions;

use JuniWalk\SSH\Authentication;

final class AuthenticationException extends SSHException
{
	/**
	 * @param  Authentication  $auth
	 * @param  string  $message
	 * @return static
	 */
	public static function fromAuth(Authentication $auth, string $message = ''): self
	{
		$type = get_class($auth);
		$type = substr($type, strrpos($type, '\\') +1);

		return new static('"'.$type.'" authentication for user "'.$auth->getUsername().'" failed. '.$message, 500);
	}
}
