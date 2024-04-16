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
		$auth = new Password('demo', 'password');
		$ftp = new SSHService('test.rebex.net', 22, $auth);

		Assert::same($ftp->isConnected(), true);
	}

	/**
	 * @throws JuniWalk\SSH\Exceptions\AuthenticationException
	 */
	public function testConnectAnonymouse(): void
	{
		// Auth None is not supported
		$ftp = new SSHService('test.rebex.net');
	}
}

(new ConnectionTest)->run();
