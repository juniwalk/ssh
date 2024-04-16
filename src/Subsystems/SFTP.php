<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Subsystems;

use JuniWalk\SSH\Authentications\Password;
use JuniWalk\SSH\Exceptions\ConnectionException;
use JuniWalk\SSH\Exceptions\FileHandlingException;
use Nette\Http\Url;

trait SFTP
{
	/** @var resource|null */
	private $sftp;


	/**
	 * @return resource
	 * @throws ConnectionException
	 */
	private function openSftp()
	{
		if (isset($this->sftp)) {
			return $this->sftp;
		}

		if (!$sftp = @ssh2_sftp($this->session)) {
			throw ConnectionException::fromLastError('Unable to open SFTP.');
		}

		return $this->sftp = $sftp;
	}


	/**
	 * @throws FileHandlingException
	 */
	public function write(string $remoteFile, string $content, int $mode = 0644): bool
	{
		$sftp = $this->openSftp();

		if (!$stream = @fopen('ssh2.sftp://'.$sftp.$remoteFile, 'w')) {
			throw FileHandlingException::fromFile($remoteFile, 'Could not open file');
		}

		if (@fwrite($stream, $content) === false) {
			throw FileHandlingException::fromFile($remoteFile, 'Could not write data to file');
		}

		@fclose($stream);

		return $this->chmod($remoteFile, $mode);
	}


	/**
	 * @throws FileHandlingException
	 */
	public function read(string $remoteFile): string
	{
		$sftp = $this->openSftp();

		if (!$stream = @fopen('ssh2.sftp://'.$sftp.$remoteFile, 'r')) {
			throw FileHandlingException::fromFile($remoteFile, 'Could not open file');
		}

		$contents = stream_get_contents($stream);
		@fclose($stream);

		if ($contents === false) {
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


	/**
	 * @throws FileHandlingException
	 */
	public function mkdir(string $path, int $mode = 0777, bool $recursive = false): bool
	{
		if (!$recursive && $this->isDir($path)) {
			throw FileHandlingException::fromFile($path, 'Directory already exists');
		}

		return ssh2_sftp_mkdir($this->openSftp(), $path, $mode, $recursive);
	}


	public function rmdir(string $path, bool $recursive = false): bool
	{
		if (!$this->isDir($path)) {
			return false;
		}

		$items = $this->list($path);

		if ($recursive) foreach ($items as $key => $item) {
			if (!$this->rmdir($item, $recursive)) {
				continue;
			}

			unset($items[$key]);
		}

		if (!empty($items)) {
			return false;
		}

		return ssh2_sftp_rmdir($this->openSftp(), $path);
	}


	/**
	 * @return string[]
	 * @throws FileHandlingException
	 */
	public function list(string $path): array
	{
		$sftp = $this->openSftp();
		$files = [];

		if (!$list = @scandir('ssh2.sftp://'.$sftp.$path)) {
			throw FileHandlingException::fromFile($path, 'Could not list directory');
		}

		foreach ($list as $key => $file) {
			if (in_array($file, ['.', '..'])) {
				continue;
			}

			$files[] = $path.'/'.$file;
		}

		return $files;
	}


	public function unlink(string $remoteFile): bool
	{
		return ssh2_sftp_unlink($this->openSftp(), $remoteFile);
	}


	public function symlink(string $remoteFile, string $link): bool
	{
		return ssh2_sftp_symlink($this->openSftp(), $remoteFile, $link);
	}


	public function chmod(string $remoteFile, int $mode = 0644): bool
	{
		return ssh2_sftp_chmod($this->openSftp(), $remoteFile, $mode);
	}


	/**
	 * @return mixed[]|false
	 */
	public function stat(string $remoteFile): array|false
	{
		return ssh2_sftp_stat($this->openSftp(), $remoteFile);
	}


	public function isDir(string $path): bool
	{
		return is_dir('ssh2.sftp://'.$this->openSftp().$path);
	}


	public function createStreamPath(string $path): string
	{
		return 'ssh2.sftp://'.$this->openSftp().$path;
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
