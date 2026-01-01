<?php
/**
 * ProviderInterface.php
 *
 * Version:     1.1.0
 * Author:      James John McGuire
 * Author URI:  http://www.digitalzenworks.com/
 * PHP version  8.1.1
 *
 * @category  PHP
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 - 2026 James John McGuire <jamesjohnmcguire@gmail.com>
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
	 * @return array
	 */
	public function process(): ?array;
}
