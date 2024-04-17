<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\SSH\Authentications\Password;
use JuniWalk\SSH\FTPService;
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

	public function testConnectFTP(): void
	{
		$ftp = new FTPService('ftp://'.HOSTNAME);
		Assert::same($ftp->getHost(), HOSTNAME);
		Assert::true($ftp->isConnected());
	}

	public function testConnectSSL(): void
	{
		$ftp = new FTPService('ftps://'.HOSTNAME);
		Assert::same($ftp->getHost(), HOSTNAME);
		Assert::true($ftp->isConnected());
	}

	public function testConnectWithoutProtocol(): void
	{
		$ftp = new FTPService(HOSTNAME);
		Assert::same($ftp->getHost(), HOSTNAME);
		Assert::true($ftp->isConnected());
	}

	public function testConnectWithPassword(): void
	{
		$auth = new Password(USERNAME, PASSWORD);
		$ftp = new FTPService(HOSTNAME, 21, $auth);

		Assert::same($ftp->getHost(), HOSTNAME);
		Assert::true($ftp->isConnected());
	}
}

(new ConnectionTest)->run();
