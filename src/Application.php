<?php declare(strict_types = 1);

namespace DrupalCodeGenerator;

use Drupal\Core\DependencyInjection\ContainerNotInitializedException;
use DrupalCodeGenerator\Command\Navigation;
use DrupalCodeGenerator\Event\ApplicationEvent;
use DrupalCodeGenerator\Event\GeneratorsEvent;
use DrupalCodeGenerator\Helper\Drupal\ConfigInfo;
use DrupalCodeGenerator\Helper\Drupal\HookInfo;
use DrupalCodeGenerator\Helper\Drupal\ModuleInfo;
use DrupalCodeGenerator\Helper\Drupal\PermissionInfo;
use DrupalCodeGenerator\Helper\Drupal\RouteInfo;
use DrupalCodeGenerator\Helper\Drupal\ServiceInfo;
use DrupalCodeGenerator\Helper\Drupal\ThemeInfo;
use DrupalCodeGenerator\Helper\Dumper\DryDumper;
use DrupalCodeGenerator\Helper\Dumper\FileSystemDumper;
use DrupalCodeGenerator\Helper\Printer\ListPrinter;
use DrupalCodeGenerator\Helper\Printer\TablePrinter;
use DrupalCodeGenerator\Helper\QuestionHelper;
use DrupalCodeGenerator\Helper\Renderer\TwigRenderer;
use DrupalCodeGenerator\Twig\TwigEnvironment;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Twig\Loader\FilesystemLoader as TemplateLoader;

/**
 * DCG console application.
 */
final class Application extends BaseApplication implements ContainerAwareInterface, EventDispatcherInterface {

  use ContainerAwareTrait;

  /**
   * Path to DCG root directory.
   */
  public const ROOT = __DIR__ . '/..';

  /**
   * DCG version.
   */
  public const VERSION = '3.0.0-dev';

  /**
   * DCG API version.
   */
  public const API = 3;

  /**
   * Path to templates directory.
   */
  public const TEMPLATE_PATH = Application::ROOT . '/templates';

  /**
   * Creates the application.
   */
  public static function create(ContainerInterface $container): self {
    $application = new self('Drupal Code Generator', self::VERSION);
    $application->setContainer($container);

    $file_system = new SymfonyFileSystem();
    $template_loader = new TemplateLoader();
    $template_loader->addPath(self::TEMPLATE_PATH . '/_lib', 'lib');
    $application->setHelperSet(
      new HelperSet([
        new QuestionHelper(),
        new DryDumper($file_system),
        new FileSystemDumper($file_system),
        new TwigRenderer(new TwigEnvironment($template_loader)),
        new ListPrinter(),
        new TablePrinter(),
        new ModuleInfo($container->get('module_handler')),
        new ThemeInfo($container->get('theme_handler')),
        new ServiceInfo($container),
        new HookInfo($container->get('module_handler')),
        new RouteInfo($container->get('router.route_provider')),
        new ConfigInfo($container->get('config.factory')),
        new PermissionInfo($container->get('user.permissions')),
      ])
    );

    $generator_factory = new GeneratorFactory(
      $application->getContainer()->get('class_resolver'),
    );
    $generators_event = new GeneratorsEvent(
      $generator_factory->getGenerators(),
    );
    $application->addCommands(
      $application->dispatch($generators_event)->generators,
    );

    $application->add(new Navigation());
    $application->setDefaultCommand('navigation');

    $application->dispatch(
      new ApplicationEvent($application),
    );
    return $application;
  }

  /**
   * Returns Drupal container.
   */
  public function getContainer(): ContainerInterface {
    if (!$this->container) {
      throw new ContainerNotInitializedException('Application::$container is not initialized yet.');
    }
    return $this->container;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Move this to a helper.
   */
  public function dispatch(object $event): object {
    return $this->getContainer()->get('event_dispatcher')->dispatch($event);
  }

}
