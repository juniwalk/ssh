<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Subsystems;

use JuniWalk\SSH\Exceptions\CommandFailedException;

trait Shell
{
	/**
	 * @param  string  $command
	 * @param  string[]  $env
	 * @return string
	 * @throws CommandFailedException
	 */
	public function exec(string $command, iterable $env = []): string
	{
        $exec = $command.'; echo -ne "[return_code:$?]"';
		$exec = '('.$command.'); echo -e "\n$?"';

		if (!$stdout = ssh2_exec($this->session, $exec, null, $env)) {
			throw new CommandFailedException($command);
		}

		$stderr = ssh2_fetch_stream($stdout, SSH2_STREAM_STDERR);

		stream_set_blocking($stdout, true);
		stream_set_blocking($stderr, true);

		$output = stream_get_contents($stdout);
		fclose($stdout);

		if (preg_match('/^(.*)\n+(0|-?[1-9][0-9]*)$/s', $output, $matches) && $matches[2] === "0") {
			return $matches[1];
		}

		throw CommandFailedException::fromStderr($command, 1/*(int) $matches[2]*/, $stderr);
	}
}
