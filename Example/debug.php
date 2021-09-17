<?php
/////////////////////////////////////////////////////////////////////////////
// Defines
/////////////////////////////////////////////////////////////////////////////
defined('E_NONE') OR define('E_NONE', 0);
defined('E_DEBUG') OR define('E_DEBUG', 4);
defined('E_WARNING') OR define('E_WARNING', 8);

if (PHP_SAPI == 'cli')
{
	defined('EOL') OR define('EOL', PHP_EOL);
}
else
{
	defined('EOL') OR define('EOL', '<br />'.PHP_EOL);
}

class Debug
{
	const ERROR = 1;
	const WARNING = 2;
	const DEBUG = 4;
	const INFO = 8;

	private $level = self::ERROR;
	private $logFile = null;

	public function __construct($level = self::ERROR, $logFile = null)
	{
		$this->level = $level;
		$this->logFile = $logFile;
	}

	public function Dump($level, $object)
	{
		if ($level <= $this->level)
		{
			self::DumpStatic($object);
		}
	}

	public static function DumpStatic($object)
	{
		var_dump($object);
		echo "<br />".PHP_EOL;
		Common::FlushBuffers();
	}

	public function DebugExit($level, $message)
	{
		if ($level <= $this->level)
		{
			exit($message);
		}
	}

	public function Log($message)
	{
		if (null != $this->logFile)
		{
			self::LogStatic($message, $this->logFile);
		}
	}

	public static function LogStatic($message, $logFile)
	{
		if (null != $logFile)
		{
			$time = date('Y-m-d H:i:s');
			file_put_contents($logFile, $time.' '.$message.PHP_EOL,
				FILE_APPEND | LOCK_EX);
		}
	}

	public function On($level = E_NOTICE)
	{
		$this->debug = $level;
	}

	public function Off($level = E_NONE)
	{
		$this->debug = $level;
	}

	public function DebugPrint($level, $statement, $label = '')
	{
		if ($level <= $this->level)
		{
			if (!empty($label))
			{
				echo $label.": ";
			}

			print_r($statement);
			echo "<br />".PHP_EOL;
			self::FlushBuffers();
		}
	}

	public static function FlushBuffers()
	{
		if (ob_get_level() > 0)
		{
			ob_flush();
		}
		flush();
	}

	public function Show($level, $message)
	{
		if ($level <= $this->level)
		{
			$this->Log($message);

			echo $message.EOL;
			self::FlushBuffers();
		}
	}

	public static function ShowStatic($message, $logFile = null)
	{
		self::LogStatic($message, $logFile);

		echo $message.EOL;
		self::FlushBuffers();
	}
}
