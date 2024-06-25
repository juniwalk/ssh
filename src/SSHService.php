<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH;

use JuniWalk\SSH\Authentications\None;
use JuniWalk\SSH\Exceptions\AuthenticationException;
use JuniWalk\SSH\Exceptions\ConnectionException;
use JuniWalk\SSH\Subsystems;

final class SSHService implements Service
{
	use Subsystems\SFTP;
	use Subsystems\Shell;

	/** @var resource */
	private $session;

	public function __construct(
		private string $host = '',
		private int $port = 22,
		private Authentication $auth = new None('root'),
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


	public function isConnected(): bool
	{
		return isset($this->session) && is_resource($this->session);	// @phpstan-ignore isset.property
	}


	/**
	 * @throws AuthenticationException
	 * @throws ConnectionException
	 */
	public function connect(string $host, int $port = 22, ?Authentication $auth = null): static
	{
		$this->isConnected() && $this->disconnect();

		if (!function_exists('ssh2_connect')) {
			throw ConnectionException::fromExtension('ext-ssh2');
		}

		$session = @ssh2_connect($host, $port);

		if (!is_resource($session)) {
			throw ConnectionException::fromLastError($host.':'.$port);
		}

		$auth ??= $this->auth;

		if (!$auth->authenticate($session)) {
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
		unset($this->sftp);

		if ($this->isConnected()) {
			ssh2_disconnect($this->session);
		}

		unset($this->session);
	}
}
