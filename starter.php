<?php
require_once "Processor.php";

ini_set('memory_limit', '-1');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

$command = null;
$providers = array();
$consumers = array();

if (PHP_SAPI == 'cli')
{
	if (!empty($argv[1]))
	{
		$command = $argv[1];
	}

	if (!empty($argv[2]))
	{
		$provider = $argv[2];
		$providers[] = $provider;
	}

	if (!empty($argv[3]))
	{
		$consumer = $argv[3];
		$consumers[] = $consumer;
	}
}
else
{
	if ((!empty($_GET)) && (!empty($_GET['command'])))
	{
		$command = $_GET['command'];
	}

	if ((!empty($_GET)) && (!empty($_GET['provider'])))
	{
		$provider = $_GET['provider'];
		$providers[] = $provider;
	}

	if ((!empty($_GET)) && (!empty($_GET['consumer'])))
	{
		$consumer = $_GET['consumer'];
		$consumers[] = $consumer;
	}
}

if (empty($command))
{
	$command = 'help';
}

$processor = new Processor($providers, $consumers);

[$processor, $command]();

?>
