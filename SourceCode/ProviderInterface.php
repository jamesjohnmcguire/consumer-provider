<?php
/**
 * ProviderInterface.php
 *
 * PHP version  8.1.1
 *
 * @category  PHP
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 - 2022 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @link      https://github.com/jamesjohnmcguire/consumer-provider
 */

declare(strict_types=1);

namespace digitalzenworks\ConsumerProvider;

/**
 * ProviderInterface
 *
 * @package ConsumerProvider
 */
interface ProviderInterface
{
	/**
	 * Function process
	 *
	 * @param string $consumer The name of the consumer interface.
	 *
	 * @return void
	 */
	public function process(string $consumer);
}
