<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2024
 * @license   MIT License
 */

use Tester\Environment;
use Tracy\Debugger;

if (@!include __DIR__.'/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

Debugger::enable(Debugger::Development);
Environment::setup();

const Hostname = 'test.rebex.net';
const Username = 'demo';
const Password = 'password';
