<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

namespace JuniWalk\SSH;

use FTP\Connection;
use JuniWalk\SSH\Authentications\None;
use JuniWalk\SSH\Exceptions\AuthenticationException;
use JuniWalk\SSH\Exceptions\ConnectionException;
use JuniWalk\SSH\Subsystems;

final class FTPService implements Service
{
	use Subsystems\FTP;

	private Connection $session;

	public function __construct(
		private string $host = '',
		private int $port = 21,
		private Authentication $auth = new None('anonymous'),
	) {
		$host && $this->connect($host, $port, $auth);
	}


	public function __destruct()
	{
		$this->disconnect();
	}


	public function getHost(): string
	{
		return $this->host;
	}


	public function getPort(): int
	{
		return $this->port;
	}


	public function setPassive(bool $passive = true): bool
	{
		return ftp_pasv($this->session, $passive);
	}


	public function isConnected(): bool
	{
		return isset($this->session);
	}


	/**
	 * @throws AuthenticationException
	 * @throws ConnectionException
	 */
	public function connect(string $host, int $port = 21, ?Authentication $auth = null): static
	{
		$this->isConnected() && $this->disconnect();

		if (!function_exists('ftp_connect')) {
			throw ConnectionException::fromExtension('ext-ftp');
		}

		if (!preg_match('#^\w+://#', $host)) {
			$host = 'ftp://'.$host;
		}

		[$schema, $host] = explode('://', $host);

		$session = match ($schema) {
			'ftps'	=> ftp_ssl_connect($host, $port),
			'ftp'	=> ftp_connect($host, $port),

			default => throw ConnectionException::fromProtocol($schema, $host.':'.$port),
		};

		if (!$session instanceof Connection) {
			throw ConnectionException::fromLastError($host.':'.$port);
		}

		$auth ??= $this->auth;

		if (!$auth->login($session)) {
			throw AuthenticationException::fromAuth($auth);
		}

		$this->session = $session;
		$this->auth = $auth;
		$this->host = $host;
		$this->port = $port;

		return $this;
	}


	public function disconnect(): void
	{
		if ($this->isConnected()) {
			ftp_close($this->session);
		}

		unset($this->session);
	}
}
