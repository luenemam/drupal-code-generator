<?php

namespace DrupalCodeGenerator\Command\Drupal_8\Service;

use DrupalCodeGenerator\Command\ModuleGenerator;
use DrupalCodeGenerator\Utils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Implements d8:service:request-policy command.
 */
class RequestPolicy extends ModuleGenerator {

  protected $name = 'd8:service:request-policy';
  protected $description = 'Generates a request policy service';
  protected $alias = 'request-policy';

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) :void {
    $questions = Utils::moduleQuestions();
    $questions['class'] = new Question('Class', 'Example');
    $this->collectVars($questions);

    $this->addFile()
      ->path('src/PageCache/{class}.php')
      ->template('d8/service/request-policy.twig');

    $this->addServicesFile()
      ->template('d8/service/request-policy.services.twig');
  }

}
