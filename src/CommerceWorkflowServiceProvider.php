<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\CommerceWorkflowServiceProvider.
 */

namespace Drupal\commerce_workflow;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\commerce_workflow\DependencyInjection\Compiler\WorkflowGuardsPass;

/**
 * Registers the workflow guard compiler pass.
 */
class CommerceWorkflowServiceProvider implements ServiceProviderInterface  {

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new WorkflowGuardsPass());
  }

}
