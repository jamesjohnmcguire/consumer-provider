<?php

/**
 * Unit Tests.
 *
 * @package   ConsumerProvider
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2021 - 2026 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.8.38
 * @link      https://github.com/jamesjohnmcguire/ConsumerProvider
 */

declare(strict_types=1);

namespace DigitalZenWorks\ConsumerProvider\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * UnitTests class.
 *
 * Contains all the automated API tests.
 */
final class UnitTests extends \PHPUnit\Framework\TestCase
{
	/**
	 * Set up before class method.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() : void
	{
	}

	/**
	 * Set up method.
	 *
	 * @return void
	 */
	protected function setUp() : void
	{
		parent::setUp();
	}

	/**
	 * Tear down method.
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
	}

	/**
	 * Sanity check test.
	 *
	 * @return void
	 */
	#[Group('basic')]
	#[Test]
	public function sanityCheck()
	{
		$tester = 18;

		$this->assertEquals(18, $tester);
	}
}
