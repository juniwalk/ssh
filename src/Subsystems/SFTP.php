<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH\Subsystems;

use JuniWalk\SSH\Exceptions\CommandFailedException;
use JuniWalk\SSH\Exceptions\FileHandlingException;

trait SFTP
{
	/** @var resource */
	private $sftp;


	/**
	 * @return resource
	 */
	private function openSftp()
	{
		if (isset($this->sftp)) {
			return $this->sftp;
		}

		return $this->sftp = ssh2_sftp($this->session);
	}


	/**
	 * @param  string  $content
	 * @param  string  $remoteFile
	 * @param  int  $mode
	 * @return bool
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
	 * @param  string  $remoteFile
	 * @return string
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
	 * @param  string  $localFile
	 * @param  string  $remoteFile
	 * @param  int  $mode
	 * @return bool
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
	 * @param  string  $remoteFile
	 * @param  string  $localFile
	 * @param  bool  $overwrite
	 * @return bool
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
	 * @param  string  $path
	 * @param  int  $mode
	 * @param  bool  $recursive
	 * @return bool
	 * @throws FileHandlingException
	 */
	public function mkdir(string $path, int $mode = 0777, bool $recursive = false): bool
	{
		if (!$recursive && $this->isDir($path)) {
			throw FileHandlingException::fromFile($path, 'Directory already exists');
		}

		return ssh2_sftp_mkdir($this->openSftp(), $path, $mode, $recursive);
	}


	/**
	 * @param  string  $path
	 * @return bool
	 */
	public function rmdir(string $path): bool
	{
		return ssh2_sftp_rmdir($this->openSftp(), $path);
	}


	/**
	 * @param  string  $path
	 * @return string[]
	 * @throws FileHandlingException
	 */
	public function list(string $path): iterable
	{
		try {
			$list = $this->exec('find '.$path.'/* -maxdepth 1');

		} catch (CommandFailedException $e) {
			throw FileHandlingException::fromFile($path, 'Could not list files in directory');
		}

		return explode(PHP_EOL, $list);
	}


	/**
	 * @param  string  $remoteFile
	 * @return bool
	 */
	public function unlink(string $remoteFile): bool
	{
		return ssh2_sftp_unlink($this->openSftp(), $remoteFile);
	}


	/**
	 * @param  string  $remoteFile
	 * @param  string  $link
	 * @return bool
	 */
	public function symlink(string $remoteFile, string $link): bool
	{
		return ssh2_sftp_symlink($this->openSftp(), $remoteFile, $link);
	}


	/**
	 * @param  string  $remoteFile
	 * @param  int  $mode
	 * @return bool
	 */
	public function chmod(string $remoteFile, int $mode = 0644): bool
	{
		return ssh2_sftp_chmod($this->openSftp(), $remoteFile, $mode);
	}


	/**
	 * @param  string  $remoteFile
	 * @return string[]|bool
	 */
	public function stat(string $remoteFile)//: iterable
	{
		return ssh2_sftp_stat($this->openSftp(), $remoteFile);
	}


	/**
	 * @param  string  $path
	 * @return bool
	 */
	public function isDir(string $path): bool
	{
		$sftp = $this->openSftp();

		return is_dir('ssh2.sftp://'.$sftp.$path);
	}
}
