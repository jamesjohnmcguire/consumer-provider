<?php
/**
 * ConsumerInterface.php
 *
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 */

namespace digitalzenworks\ConsumerProvider;

/**
 * ConsumerInterface
 *
 * @package ConsumerProvider
 */
interface ConsumerInterface
{
	/**
	 * Function process
	 *
	 * @param string $data The data for the consumer.
	 *
	 * @return void
	 */
	public function process(string $data);
}
