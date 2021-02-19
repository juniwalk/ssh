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

	/** @var resource */
	private $session;

	/** @var Authentication */
	private $auth;

	/** @var string */
	private $host;

	/** @var int */
	private $port;


	/**
	 * @param Authentication  $auth
	 */
	public function __construct(Authentication $auth = null)
	{
		$this->auth = $auth ?: (new None('root'));
	}


	public function __destruct()
	{
		$this->disconnect();
	}


	/**
	 * @return string|null
	 */
	public function getHost(): ?string
	{
		return $this->host;
	}


	/**
	 * @return int|null
	 */
	public function getPort(): ?int
	{
		return $this->port;
	}


	/**
	 * @return bool
	 */
	public function isConnected(): bool
	{
		return is_resource($this->session);
	}


	/**
	 * @param  string  $host
	 * @param  int  $port
	 * @param  Authentication|null  $auth
	 * @return void
	 * @throws ConnectionException
	 */
	public function connect(string $host, int $port = 22, Authentication $auth = null): void
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
	}


	/**
	 * @return void
	 */
	public function disconnect(): void
	{
		$this->sftp = null;

		if (is_resource($this->session)) {
			ssh2_disconnect($this->session);
		}

		$this->session = null;
	}
}
