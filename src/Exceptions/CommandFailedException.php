<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Exceptions;

final class CommandFailedException extends SSHException
{
	/**
	 * @param  string  $command
	 * @param  int  $code
	 * @param  resource  $stderr
	 * @return static
	 */
	public static function fromStderr(string $command, int $code, $stderr): self
	{
		$command = '$ '.$command.';';

		if ($error = stream_get_contents($stderr)) {
			$command .= PHP_EOL.$error;
		}

		fclose($stderr);

		return new static($command, $code);
	}
}
