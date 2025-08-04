<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

namespace JuniWalk\SSH\Subsystems;

use JuniWalk\SSH\Authentications\Password;
use JuniWalk\SSH\Exceptions\FileHandlingException;
use Nette\Http\Url;

trait FTP
{
	/**
	 * @throws FileHandlingException
	 */
	public function write(string $remoteFile, string $content, int $mode = 0644): bool
	{
		if (!$stream = @fopen('php://temp', 'r+')) {
			throw FileHandlingException::fromLastError('Unable to open temp file.');
		}

		fwrite($stream, $content);
		rewind($stream);

		if (!ftp_fput($this->session, $remoteFile, $stream)) {
			throw FileHandlingException::fromFile($remoteFile, 'Could not write data to file');
		}

		fclose($stream);

		return (bool) $this->chmod($remoteFile, $mode);
	}


	/**
	 * @throws FileHandlingException
	 */
	public function read(string $remoteFile): string
	{
		if (!$stream = @fopen('php://temp', 'r+')) {
			throw FileHandlingException::fromLastError('Unable to open temp file.');
		}

		if (!ftp_fget($this->session, $stream, $remoteFile)) {
			throw FileHandlingException::fromFile($remoteFile, 'Could not write data to file');
		}

		$contents = stream_get_contents($stream);
		fclose($stream);

		if ($contents === false) { // @phpstan-ignore identical.alwaysFalse (Bug phpstan/phpstan#13289)
			throw FileHandlingException::fromFile($remoteFile, 'Unable to read remote file');
		}

		return $contents;
	}


	/**
	 * @throws FileHandlingException
	 */
	public function send(string $localFile, string $remoteFile, int $mode = 0644): bool
	{
		$content = @file_get_contents($localFile);

		if ($content === false) {
			throw FileHandlingException::fromFile($localFile, 'Could not open local file');
		}

		return $this->write($remoteFile, $content, $mode);
	}


	/**
	 * @throws FileHandlingException
	 */
	public function receive(string $remoteFile, string $localFile, bool $overwrite = true): bool
	{
		if (!$overwrite && is_file($localFile)) {
			throw FileHandlingException::fromFile($localFile, 'Local file already exists');
		}

		return file_put_contents($localFile, $this->read($remoteFile)) !== false;
	}


	public function unlink(string $remoteFile): bool
	{
		return ftp_delete($this->session, $remoteFile);
	}


	public function chmod(string $remoteFile, int $mode = 0644): int|false
	{
		return ftp_chmod($this->session, $mode, $remoteFile);
	}


	public function createWebPath(string $path): Url
	{
		$url = new Url;
		$url->setScheme('https');
		$url->setUser($this->auth->getUsername());
		$url->setHost($this->host);
		$url->setPath($path);

		if ($this->auth instanceof Password) {
			$url->setPassword($this->auth->getPassword());
		}

		return $url;
	}
}
