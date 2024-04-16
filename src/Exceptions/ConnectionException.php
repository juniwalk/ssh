<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Exceptions;

final class ConnectionException extends SSHException
{
	public static function fromExtension(string $ext): self
	{
		return new static('Extension "'.$ext.'" is not loaded.', 500);
	}


	public static function fromLastError(string $message): self
	{
		$lastError = error_get_last()['message'] ?? '';
		return new static($message.' | '.$lastError, 500);
	}


	public static function fromProtocol(string $protocol, string $host): self
	{
		return new static('Unknown protocol "'.$protocol.'" used for "'.$host.'".', 500);
	}
}
