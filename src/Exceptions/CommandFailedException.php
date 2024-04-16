<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Exceptions;

final class CommandFailedException extends SSHException
{
	/**
	 * @param resource|false $stderr
	 */
	public static function fromStderr(string $command, int $code, $stderr): self
	{
		$message = '$ '.$command.';'.PHP_EOL;

		if ($stderr !== false) {
			$message .= stream_get_contents($stderr);
			fclose($stderr);
		}

		return new static(trim($message), $code);
	}


	public static function fromLastError(string $command): self
	{
		$lastError = error_get_last()['message'] ?? '';
		$message = '$ '.$command.';'.PHP_EOL;

		return new static($message.$lastError, 500);
	}
}
