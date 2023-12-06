<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Exceptions;

final class ConnectionException extends SSHException
{
	public static function fromLastError(string $message): self
	{
		$lastError = error_get_last();
		return new static($message.' | '.$lastError['message'], 500);
	}
}
