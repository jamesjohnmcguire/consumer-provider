<?php
define("BASE_PATH", __DIR__);

class Processor
{
	public $logFile = null;

	public $consumers = null;
	public $providers = null;

	public function __construct(
		array $providerClassNames, array $consumerClassNames)
	{
		$this->consumers = $consumerClassNames;
		$this->providers = $providerClassNames;
	}

	public function Help()
	{
		chdir('..');
		$files = glob("*.php");

		$providers = array();
		$consumers = array();

		foreach($files as $file)
		{
			$implementer = self::GetImplementers('ConsumerInterface', $file);

			if (!empty($implementer))
			{
				$consumers[] = $implementer;
			}

			$implementer = self::GetImplementers('ProviderInterface', $file);

			if (!empty($implementer))
			{
				$providers[] = $implementer;
			}
		}

		echo "usage: php starter.php <command> <provider> <consumer> " . EOL;
		echo "available commands: Process".EOL;

		self::ShowImplementers('providers', $providers);
		self::ShowImplementers('consumers', $consumers);
	}

	public function Process()
	{
		echo "Processing...\r\n";
		if (!empty($this->providers))
		{
			foreach ($this->providers as $providerName)
			{
				require_once BASE_PATH . "/../$providerName.php";
				$provider = new $providerName();

				foreach ($this->consumers as $consumerName)
				{
					require_once BASE_PATH . "/../$consumerName.php";
					$consumer = new $consumerName();
					$list = $provider->Process($consumer);
				}
			}
		}
	}

	private static function GetClassNameFromFile($fileName, $includeNamespace)
	{
		$contents = file_get_contents($fileName);
 
		$namespace = $class = "";
 
		// Set helper values to know that we have found the namespace/class
		// token and need to collect the string values after them
		$gettingNamespace = $gettingClass = false;
 
		// Go through each token and evaluate it as necessary
		foreach (token_get_all($contents) as $token)
		{
			// If this token is the namespace declaring, then flag that
			// the next tokens will be the namespace name
			if (is_array($token) && $token[0] == T_NAMESPACE)
			{
				$gettingNamespace = true;
			}
 
			// If this token is the class declaring, then flag that the
			// next tokens will be the class name
			if (is_array($token) && $token[0] == T_CLASS)
			{
				$gettingClass = true;
			}
 
			if ($gettingNamespace === true)
			{
				// If the token is a string or the namespace separator...
				if (is_array($token) &&
					in_array($token[0], [T_STRING, T_NS_SEPARATOR]))
				{
					$namespace .= $token[1];
				}
				else if ($token === ';')
				{
 					// If the token is the semicolon, then we're done with
					// the namespace declaration
					$gettingNamespace = false;
				}
			}
 
			if ($gettingClass === true)
			{
				// If the token is a string, it's the name of the class
				if (is_array($token) && $token[0] == T_STRING)
				{
					$class = $token[1];
					break;
				}
			}
		}
 
		// Build the fully-qualified class name and return it
		if (($includeNamespace == false) || (empty($namespace)))
		{
			$fullClass = $class;
		}
		else
		{
			$fullClass = $namespace . '\\' . $class;
		}

		return $fullClass;
	}

	private static function GetImplementers($interface, $file)
	{
		$implementer = null;

		$contents = file_get_contents($file);
		$position = strpos($contents, "implements $interface");

		if ($position !== false)
		{
			$class = self::GetClassNameFromFile($file, false);

			if ($class != 'DataMiner')
			{
				$implementer = $class;
			}
		}

		return $implementer;
	}

	private static function ShowImplementers($name, $implementers)
	{
		echo "available $name: ";
		$first = true;
		foreach ($implementers as $implementer)
		{
			if ($first == false)
			{
				echo ', ';
			}

			echo $implementer;
			$first = false;
		}

		echo EOL;
	}

	private function GetConsumer($consumerId)
	{
		require_once $consumerId . ".php";
		$consumer = new $consumerId();

		return $consumer;
	}

	private function GetProvider($providerId)
	{
		require_once $providerId . ".php";
		$provider = new $providerId();

		return $provider;
	}
}
