<?php declare(strict_types = 1);

namespace DrupalCodeGenerator\Event;

use DrupalCodeGenerator\Asset\AssetCollection;

/**
 * Fired when altering rendered assets.
 */
final class PreProcessEvent {

  use StoppableEventTrait;

  /**
   * Constructs the event object.
   */
  public function __construct(
    public readonly AssetCollection $assets,
    public readonly string $commandName,
    public readonly bool $isDry,
    public string $destination,
  ) {}

}
