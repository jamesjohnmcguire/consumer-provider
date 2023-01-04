<?php
/**
 * ConsumerInterface.php
 *
 * Version:     1.1.0
 * Author:      James John McGuire
 * Author URI:  http://www.digitalzenworks.com/
 * PHP version  8.1.1
 *
 * @category  PHP
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 - 2023 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @link      https://github.com/jamesjohnmcguire/consumer-provider
 */

declare(strict_types=1);

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
	 * @param array $data The data to be consumed.
	 *
	 * @return void
	 */
	public function process(?array $data): void;
}
