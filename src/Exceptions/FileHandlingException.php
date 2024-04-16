<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Exceptions;

final class FileHandlingException extends SSHException
{
	public static function fromLastError(string $message): self
	{
		$lastError = error_get_last()['message'] ?? '';
		return new static($message.' | '.$lastError, 500);
	}


	public static function fromFile(string $file, string $message = null): self
	{
		if (isset($message)) {
			$file = $message.': '.$file;
		}

		return new static($file);
	}
}
