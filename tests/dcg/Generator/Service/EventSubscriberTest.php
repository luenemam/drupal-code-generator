<?php

namespace DrupalCodeGenerator\Tests\Generator\Service;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for service/event-subscriber command.
 */
final class EventSubscriberTest extends BaseGeneratorTest {

  protected $class = 'Service\EventSubscriber';

  protected $interaction = [
    'Module name [%default_name%]:' => 'Foo',
    'Module machine name [foo]:' => 'foo',
    'Class [FooSubscriber]:' => 'BarSubscriber',
  ];

  protected $fixtures = [
    'foo.services.yml' => '/_event_subscriber.services.yml',
    'src/EventSubscriber/BarSubscriber.php' => '/_event_subscriber.php',
  ];

}
