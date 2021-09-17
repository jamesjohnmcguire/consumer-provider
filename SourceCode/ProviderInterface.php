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
