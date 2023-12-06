<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Subsystems;

use JuniWalk\SSH\Command;
use JuniWalk\SSH\Exceptions\CommandFailedException;

trait Shell
{
	/**
	 * @throws CommandFailedException
	 */
	public function exec(Command|string $command, array $env = [], bool $throwErrors = true): string
	{
		error_clear_last();

		if ($command instanceof Command) {
			$command = $command->create();
		}

		$exec = $command.'; echo -ne "[return_code:$?]"';

		if (!$stdout = @ssh2_exec($this->session, $exec, null, $env)) {
			throw CommandFailedException::fromLastError($command);
		}

		$stderr = ssh2_fetch_stream($stdout, SSH2_STREAM_STDERR);

		stream_set_blocking($stderr, true);
		stream_set_blocking($stdout, true);

		$output = stream_get_contents($stdout);
		fclose($stdout);

		if ($throwErrors && preg_match('/\[return_code:(.*?)\]/', $output, $match) && $match[1] !== '0') {
			throw CommandFailedException::fromStderr($command, (int) $match[1], $stderr);
		}

		fclose($stderr);

		return preg_replace('/\[return_code:(.*?)\]$/i', '', $output);
	}
}
