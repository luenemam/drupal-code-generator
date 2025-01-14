<?php declare(strict_types = 1);

namespace DrupalCodeGenerator\Helper\Drupal;

use Drupal\user\PermissionHandlerInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 * A helper that provides information about permissions.
 */
final class PermissionInfo extends Helper {

  /**
   * Constructs the helper.
   */
  public function __construct(
    private readonly PermissionHandlerInterface $permissionHandler,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'permission_info';
  }

  /**
   * Gets names of all available permissions.
   */
  public function getPermissionNames(): array {
    return \array_keys($this->permissionHandler->getPermissions());
  }

}
