<?php declare(strict_types = 1);

namespace DrupalCodeGenerator\Helper\Drupal;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 * A helper that provides information about installed Drupal modules.
 */
final class ModuleInfo extends Helper implements ExtensionInfoInterface {

  public function __construct(private readonly ModuleHandlerInterface $moduleHandler) {}

  public function getName(): string {
    return 'module_info';
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensions(): array {
    $modules = [];
    foreach ($this->moduleHandler->getModuleList() as $machine_name => $module) {
      $modules[$machine_name] = $this->moduleHandler->getName($machine_name);
    }
    return $modules;
  }

  /**
   * {@inheritdoc}
   */
  public function getDestination(string $machine_name, bool $is_new): ?string {
    $modules_dir = \is_dir(\DRUPAL_ROOT . '/modules/custom') ?
      'modules/custom' : 'modules';

    if ($is_new) {
      $destination = $modules_dir;
    }
    else {
      $destination = \array_key_exists($machine_name, $this->getExtensions())
        ? $this->moduleHandler->getModule($machine_name)->getPath()
        : $modules_dir . '/' . $machine_name;
    }

    return \DRUPAL_ROOT . '/' . $destination;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensionName(string $machine_name): ?string {
    return $this->getExtensions()[$machine_name] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensionMachineName(string $name): ?string {
    return \array_search($name, $this->getExtensions()) ?: NULL;
  }

  /**
   * Gets module info for a given absolute path.
   */
  public function getExtensionFromPath(string $path): ?Extension {
    if (!\str_starts_with($path, '/')) {
      throw new \InvalidArgumentException('The path must be absolute.');
    }
    foreach ($this->moduleHandler->getModuleList() as $module) {
      if (\str_starts_with($path, \DRUPAL_ROOT . '/' . $module->getPath())) {
        return $module;
      }
    }
    return NULL;
  }

}
