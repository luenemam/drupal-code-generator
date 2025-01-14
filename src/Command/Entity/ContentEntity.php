<?php declare(strict_types = 1);

namespace DrupalCodeGenerator\Command\Entity;

use DrupalCodeGenerator\Application;
use DrupalCodeGenerator\Asset\AssetCollection;
use DrupalCodeGenerator\Attribute\Generator;
use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\GeneratorType;

#[Generator(
  name: 'entity:content',
  description: 'Generates content entity',
  aliases: ['content-entity'],
  templatePath: Application::TEMPLATE_PATH . '/Entity/_content-entity',
  type: GeneratorType::MODULE_COMPONENT,
)]
final class ContentEntity extends BaseGenerator {

  protected function generate(array &$vars, AssetCollection $assets): void {
    $ir = $this->createInterviewer($vars);
    $vars['machine_name'] = $ir->askMachineName();
    $vars['name'] = $ir->askName();

    $vars['entity_type_label'] = $ir->ask('Entity type label', '{name}');
    $vars['entity_type_id'] = $ir->ask('Entity type ID', '{entity_type_label|h2m}');
    $vars['entity_base_path'] = $ir->ask('Entity base path', '/{entity_type_id|u2h}');
    $vars['fieldable'] = $ir->confirm('Make the entity type fieldable?');
    $vars['revisionable'] = $ir->confirm('Make the entity type revisionable?', FALSE);
    $vars['translatable'] = $ir->confirm('Make the entity type translatable?', FALSE);
    $vars['bundle'] = $ir->confirm('The entity type has bundle?', FALSE);
    $vars['canonical'] = $ir->confirm('Create canonical page?');
    $vars['template'] = $vars['canonical'] && $ir->confirm('Create entity template?');
    $vars['access_controller'] = $ir->confirm('Create CRUD permissions?', FALSE);

    $vars['label_base_field'] = $ir->confirm('Add "label" base field?');
    $vars['status_base_field'] = $ir->confirm('Add "status" base field?');
    $vars['created_base_field'] = $ir->confirm('Add "created" base field?');
    $vars['changed_base_field'] = $ir->confirm('Add "changed" base field?');
    $vars['author_base_field'] = $ir->confirm('Add "author" base field?');
    $vars['description_base_field'] = $ir->confirm('Add "description" base field?');
    $vars['has_base_fields'] = $vars['label_base_field'] || $vars['status_base_field'] ||
                               $vars['created_base_field'] || $vars['changed_base_field'] ||
                               $vars['author_base_field'] || $vars['description_base_field'];

    $vars['rest_configuration'] = $ir->confirm('Create REST configuration for the entity?', FALSE);

    if ($vars['entity_base_path'][0] != '/') {
      $vars['entity_base_path'] = '/' . $vars['entity_base_path'];
    }

    if (($vars['fieldable_no_bundle'] = $vars['fieldable'] && !$vars['bundle'])) {
      $vars['configure'] = 'entity.{entity_type_id}.settings';
    }
    elseif ($vars['bundle']) {
      $vars['configure'] = 'entity.{entity_type_id}_type.collection';
    }

    $vars['class_prefix'] = '{entity_type_id|camelize}';
    $vars['template_name'] = '{entity_type_id|u2h}.html.twig';

    // Contextual links need title suffix to be added to entity template.
    if ($vars['template']) {
      $assets->addFile('{machine_name}.links.contextual.yml', 'model.links.contextual.yml.twig')
        ->appendIfExists();
    }
    $assets->addFile('{machine_name}.links.action.yml', 'model.links.action.yml.twig')
      ->appendIfExists();
    $assets->addFile('{machine_name}.links.menu.yml', 'model.links.menu.yml.twig')
      ->appendIfExists();
    $assets->addFile('{machine_name}.links.task.yml', 'model.links.task.yml.twig')
      ->appendIfExists();
    $assets->addFile('{machine_name}.permissions.yml', 'model.permissions.yml.twig')
      ->appendIfExists();
    $assets->addFile('src/Entity/{class_prefix}.php', 'src/Entity/Example.php.twig');
    $assets->addFile('src/{class_prefix}Interface.php', 'src/ExampleInterface.php.twig');

    if (!$vars['canonical']) {
      $assets->addFile('src/Routing/{class_prefix}HtmlRouteProvider.php', 'src/Routing/ExampleHtmlRouteProvider.php.twig');
    }

    $assets->addFile('src/{class_prefix}ListBuilder.php', 'src/ExampleListBuilder.php.twig');
    $assets->addFile('src/Form/{class_prefix}Form.php', 'src/Form/ExampleForm.php.twig');

    if ($vars['fieldable_no_bundle']) {
      $assets->addFile('{machine_name}.routing.yml', 'model.routing.yml.twig')
        ->appendIfExists();
      $assets->addFile('src/Form/{class_prefix}SettingsForm.php', 'src/Form/ExampleSettingsForm.php.twig');
    }

    if ($vars['template']) {
      $assets->addFile('templates/{entity_type_id|u2h}.html.twig', 'templates/model-example.html.twig.twig');
      $assets->addFile('{machine_name}.module', 'model.module.twig')
        ->appendIfExists(7);
    }

    if ($vars['access_controller']) {
      $assets->addFile('src/{class_prefix}AccessControlHandler.php', 'src/ExampleAccessControlHandler.php.twig');
    }

    if ($vars['rest_configuration']) {
      $assets->addFile('config/optional/rest.resource.entity.{entity_type_id}.yml', 'config/optional/rest.resource.entity.example.yml.twig');
    }

    if ($vars['bundle']) {
      $assets->addFile('config/schema/{machine_name}.entity_type.schema.yml', 'config/schema/model.entity_type.schema.yml.twig')
        ->appendIfExists();
      $assets->addFile('src/{class_prefix}TypeListBuilder.php', 'src/ExampleTypeListBuilder.php.twig');
      $assets->addFile('src/Entity/{class_prefix}Type.php', 'src/Entity/ExampleType.php.twig');
      $assets->addFile('src/Form/{class_prefix}TypeForm.php', 'src/Form/ExampleTypeForm.php.twig');
    }

  }

}
