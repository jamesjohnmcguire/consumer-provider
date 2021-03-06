<?php
/**
 * Starter.php
 *
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 - 2022 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 */

namespace digitalzenworks\ConsumerProvider;

require_once 'Processor.php';

ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

$command = null;
$providers = [];
$consumers = [];

if (PHP_SAPI === 'cli')
{
	if (empty($argv[1]) === false)
	{
		$command = $argv[1];
	}

	if (empty($argv[2]) === false)
	{
		$provider = $argv[2];
		$providers[] = $provider;
	}

	if (empty($argv[3]) === false)
	{
		$consumer = $argv[3];
		$consumers[] = $consumer;
	}
}
else
{
	if ((empty($_GET) === false) && (empty($_GET['command']) === false))
	{
		$command = $_GET['command'];
	}

	if ((empty($_GET) === false) && (empty($_GET['provider']) === false))
	{
		$provider = $_GET['provider'];
		$providers[] = $provider;
	}

	if ((empty($_GET) === false) && (empty($_GET['consumer']) === false))
	{
		$consumer = $_GET['consumer'];
		$consumers[] = $consumer;
	}
}

if (empty($command) === true)
{
	$command = 'help';
}

$processor = new Processor($providers, $consumers);

[$processor, $command]();
