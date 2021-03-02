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
	 * @param  Command|string  $command
	 * @param  string[]  $env
	 * @return string
	 * @throws CommandFailedException
	 */
	public function exec($command, iterable $env = []): string
	{
		if ($command instanceof Command) {
			$command = $command->create();
		}

		$exec = $command.'; echo -ne "[return_code:$?]"';

		if (!$stdout = ssh2_exec($this->session, $exec, null, $env)) {
			throw new CommandFailedException($command);
		}

		$stderr = ssh2_fetch_stream($stdout, SSH2_STREAM_STDERR);

		stream_set_blocking($stderr, true);
		stream_set_blocking($stdout, true);

		$output = stream_get_contents($stdout);
		fclose($stdout);

		if (preg_match('/\[return_code:(.*?)\]/', $output, $match) && $match[1] !== '0') {
			throw CommandFailedException::fromStderr($command, (int) $match[1], $stderr);
		}

		fclose($stderr);

		return preg_replace('/\[return_code:(.*?)\]$/i', '', $output);
	}
}
