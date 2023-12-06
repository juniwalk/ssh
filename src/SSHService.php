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

final class SSHService
{
	use Subsystems\SFTP;
	use Subsystems\Shell;

	private Authentication $auth;
	private string $host;
	private int $port;
	private $session;

	public function __construct(Authentication $auth = null)
	{
		$this->auth = $auth ?: (new None('root'));
	}


	public function __destruct()
	{
		$this->disconnect();
	}


	public function getHost(): ?string
	{
		return $this->host;
	}


	public function getPort(): ?int
	{
		return $this->port;
	}


	public function isConnected(): bool
	{
		return is_resource($this->session);
	}


	/**
	 * @throws ConnectionException
	 */
	public function connect(string $host, int $port = 22, Authentication $auth = null): static
	{
		// If there already is an active connection
		$this->isConnected() && $this->disconnect();

		$session = @ssh2_connect($host, $port);
		$auth = $auth ?: $this->auth;

		if (!is_resource($session)) {
			throw ConnectionException::fromLastError($host.':'.$port);
		}

		if (!$auth || !$auth->authenticate($session)) {
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
		$this->sftp = null;

		if (is_resource($this->session)) {
			ssh2_disconnect($this->session);
		}

		$this->session = null;
	}
}
