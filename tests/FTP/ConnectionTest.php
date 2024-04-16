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
		$ftp = new FTPService('ftp://test.rebex.net');
		Assert::same($ftp->isConnected(), true);
		Assert::same($ftp->getHost(), 'test.rebex.net');
	}

	public function testConnectSSL(): void
	{
		$ftp = new FTPService('ftps://test.rebex.net');
		Assert::same($ftp->isConnected(), true);
		Assert::same($ftp->getHost(), 'test.rebex.net');
	}

	public function testConnectWithoutProtocol(): void
	{
		$ftp = new FTPService('test.rebex.net');
		Assert::same($ftp->isConnected(), true);
		Assert::same($ftp->getHost(), 'test.rebex.net');
	}

	public function testConnectWithPassword(): void
	{
		$auth = new Password('demo', 'password');
		$ftp = new FTPService('test.rebex.net', 21, $auth);

		Assert::same($ftp->isConnected(), true);
		Assert::same($ftp->getHost(), 'test.rebex.net');
	}
}

(new ConnectionTest)->run();
