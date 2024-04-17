<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\SSH\Authentications\Password;
use JuniWalk\SSH\SSHService;
use Tester\Assert;
use Tester\TestCase;

require '../bootstrap.php';

/**
 * @testCase
 */
final class ConnectionTest extends TestCase
{
	public function setUp() {}
	public function tearDown() {}

	public function testConnect(): void
	{
		$auth = new Password(USERNAME, PASSWORD);
		$ftp = new SSHService(HOSTNAME, 22, $auth);

		Assert::same($ftp->getHost(), HOSTNAME);
		Assert::true($ftp->isConnected());
	}

	/**
	 * @throws JuniWalk\SSH\Exceptions\AuthenticationException
	 */
	public function testConnectAnonymouse(): void
	{
		// Auth None is not supported
		$ftp = new SSHService(HOSTNAME);
	}
}

(new ConnectionTest)->run();
