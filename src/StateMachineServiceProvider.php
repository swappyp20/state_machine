<?php

/**
 * @file
 * Contains \Drupal\state_machine\StateMachineServiceProvider.
 */

namespace Drupal\state_machine;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\state_machine\DependencyInjection\Compiler\WorkflowGuardsPass;

/**
 * Registers the workflow guard compiler pass.
 */
class StateMachineServiceProvider implements ServiceProviderInterface  {

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new WorkflowGuardsPass());
  }

}
