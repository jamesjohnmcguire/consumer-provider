<?php
/**
 * ProviderInterface.php
 *
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 */

namespace digitalzenworks\ConsumerProvider;

define('BASE_PATH', __DIR__);

/**
 * Processor
 */
class Processor
{
	/**
	 * Log file
	 *
	 * @var string
	 */
	public $logFile = null;

	/**
	 * Consumers
	 *
	 * @var array
	 */
	public $consumers = null;

	/**
	 * Providers
	 *
	 * @var array
	 */
	public $providers = null;

	/**
	 * Constructor
	 *
	 * @param array $providerClassNames The names of the provider classes.
	 * @param array $consumerClassNames The names of the consumer classes.
	 *
	 * @return void
	 */
	public function __construct(
		array $providerClassNames,
		array $consumerClassNames)
	{
		$this->consumers = $consumerClassNames;
		$this->providers = $providerClassNames;
	}

	/**
	 * Function help
	 *
	 * @return void
	 */
	public function help()
	{
		chdir('..');
		$files = glob('*.php');

		$providers = [];
		$consumers = [];

		foreach($files as $file)
		{
			$implementer = self::getImplementers('ConsumerInterface', $file);

			if (empty($implementer) === false)
			{
				$consumers[] = $implementer;
			}

			$implementer = self::getImplementers('ProviderInterface', $file);

			if (empty($implementer) === false)
			{
				$providers[] = $implementer;
			}
		}

		echo 'usage: php starter.php <command> <provider> <consumer> ' . EOL;
		echo 'available commands: Process' . EOL;

		self::showImplementers('providers', $providers);
		self::showImplementers('consumers', $consumers);
	}

	/**
	 * Function process
	 *
	 * @return void
	 */
	public function process()
	{
		echo "Processing...\r\n";
		if (empty($this->providers) === false)
		{
			foreach ($this->providers as $providerName)
			{
				include_once BASE_PATH . "/../$providerName.php";
				$provider = new $providerName();

				foreach ($this->consumers as $consumerName)
				{
					include_once BASE_PATH . "/../$consumerName.php";
					$consumer = new $consumerName();
					$list = $provider->process($consumer);
				}
			}
		}
	}

	/**
	 * Function getClassNameFromFile
	 *
	 * @param string  $fileName         The file name to search in.
	 * @param boolean $includeNamespace Whether to include the namespace.
	 *
	 * @return string
	 */
	private static function getClassNameFromFile(
		string $fileName,
		bool $includeNamespace)
	{
		$class = '';
		$namespace = '';
		$contents = file_get_contents($fileName);

		// Set helper values to know that we have found the namespace/class
		// token and need to collect the string values after them.
		$gettingClass = false;
		$gettingNamespace = false;

		// Go through each token and evaluate it as necessary.
		foreach (token_get_all($contents) as $token)
		{
			// If this token is the namespace declaring, then flag that
			// the next tokens will be the namespace name.
			if (is_array($token) === true && $token[0] === T_NAMESPACE)
			{
				$gettingNamespace = true;
			}
 
			// If this token is the class declaring, then flag that the
			// next tokens will be the class name.
			if (is_array($token) === true && $token[0] === T_CLASS)
			{
				$gettingClass = true;
			}
 
			if ($gettingNamespace === true)
			{
				// If the token is a string or the namespace separator...
				if (is_array($token) === true &&
					in_array($token[0], [T_STRING, T_NS_SEPARATOR]) === true)
				{
					$namespace .= $token[1];
				}
				elseif ($token === ';')
				{
 					// If the token is the semicolon, then we're done with
					// the namespace declaration.
					$gettingNamespace = false;
				}
			}
 
			if ($gettingClass === true)
			{
				// If the token is a string, it's the name of the class.
				if (is_array($token) === true && $token[0] === T_STRING)
				{
					$class = $token[1];
					break;
				}
			}
		}
 
		// Build the fully-qualified class name and return it.
		if (($includeNamespace === false) || (empty($namespace) === true))
		{
			$fullClass = $class;
		}
		else
		{
			$fullClass = $namespace . '\\' . $class;
		}

		return $fullClass;
	}

	/**
	 * Function getImplementers
	 *
	 * @param string $interface The interface to use.
	 * @param string $file      The file to search in.
	 *
	 * @return string The name of the class.
	 */
	private static function getImplementers(string $interface, string $file) : string
	{
		$implementer = null;

		$contents = file_get_contents($file);
		$position = strpos($contents, "implements $interface");

		if ($position !== false)
		{
			$class = self::getClassNameFromFile($file, false);
		}

		return $implementer;
	}

	/**
	 * Function showImplementers
	 *
	 * @param string $name         The name of the interface.
	 * @param array  $implementers An array of implementers.
	 *
	 * @return void
	 */
	private static function showImplementers(string $name, array $implementers) : string
	{
		echo "available $name: ";
		$first = true;
		foreach ($implementers as $implementer)
		{
			if ($first === false)
			{
				echo ', ';
			}

			echo $implementer;
			$first = false;
		}

		echo EOL;
	}

	/**
	 * Function getConsumer
	 *
	 * @param string $consumerId The consumer name.
	 *
	 * @return string The class of the consumer.
	 */
	private function getConsumer(string $consumerId) : string
	{
		include_once $consumerId . '.php';
		$consumer = new $consumerId();

		return $consumer;
	}

	/**
	 * Function getProvider
	 *
	 * @param string $providerId The provider name.
	 *
	 * @return string The class of the provider.
	 */
	private function getProvider(string $providerId) : string
	{
		include_once $providerId . '.php';
		$provider = new $providerId();

		return $provider;
	}
}
