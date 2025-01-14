<?php declare(strict_types = 1);

namespace DrupalCodeGenerator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Base class for DCG tests.
 *
 * @package DrupalCodeGenerator\Tests
 */
abstract class BaseTestCase extends TestCase {

  /**
   * Working directory.
   */
  protected string $directory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->directory = \sys_get_temp_dir() . '/dcg_sandbox';
    (new Filesystem())->mkdir($this->directory);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    parent::tearDown();
    (new Filesystem())->remove($this->directory);
  }

}
