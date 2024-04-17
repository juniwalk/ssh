<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\SSH\Authentications\Password;
use JuniWalk\SSH\Exceptions\AuthenticationException;
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

	public function testConnectAnonymouse(): void
	{
		Assert::exception(
			fn() => new SSHService(HOSTNAME),
			AuthenticationException::class,
			'"%w%" authentication for user "%w%" failed. Method not available%A%',
		);
	}
}

(new ConnectionTest)->run();
