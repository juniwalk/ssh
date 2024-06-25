<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use JuniWalk\SSH\Command;
use Tester\Assert;
use Tester\TestCase;

require __DIR__.'/../bootstrap.php';

/**
 * @testCase
 */
final class CommandTest extends TestCase
{
	public function setUp() {}
	public function tearDown() {}

	public function testSingleOption(): void
	{
		$cmd = new Command('cat');
		$cmd->setOption('/etc/debian_version');

		Assert::same($cmd->create(), 'cat /etc/debian_version');
	}

	public function testMultipleOptions(): void
	{
		$cmd = new Command('cut');
		$cmd->setOption('--delimiter', ':');
		$cmd->setOption('--fields', '1');

		Assert::same($cmd->create(), 'cut --delimiter : --fields 1');
	}

	public function testMultipleValues(): void
	{
		$cmd = new Command('cut');
		$cmd->setOption('--delimiter', ':', '1');

		Assert::same($cmd->create(), 'cut --delimiter : --delimiter 1');
	}
}

(new CommandTest)->run();
