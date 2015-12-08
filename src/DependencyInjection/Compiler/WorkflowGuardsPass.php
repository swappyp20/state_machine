<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\DependencyInjection\Compiler\WorkflowGuardsPass.
 */

namespace Drupal\commerce_workflow\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds the context provider service IDs to the context manager.
 */
class WorkflowGuardsPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   *
   * Passes the grouped service IDs of workflow guards to the guard factory.
   */
  public function process(ContainerBuilder $container) {
    $guards = [];
    foreach ($container->findTaggedServiceIds('commerce_workflow.workflow_guard') as $id => $attributes) {
      if (empty($attributes[0]['group'])) {
        continue;
      }

      $guards[$attributes[0]['group']][] = $id;
    }

    $definition = $container->getDefinition('commerce_workflow.workflow_guard_factory');
    $definition->addArgument($guards);
  }

}
