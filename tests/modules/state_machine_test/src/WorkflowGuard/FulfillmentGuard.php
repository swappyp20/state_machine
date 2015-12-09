<?php

/**
 * @file
 * Contains \Drupal\state_machine_test\WorkflowGuard\FulfillmentGuard.
 */

namespace Drupal\state_machine_test\WorkflowGuard;

use Drupal\state_machine\WorkflowGuard\WorkflowGuardInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowTransition;
use Drupal\Core\Entity\EntityInterface;

class FulfillmentGuard implements WorkflowGuardInterface {

  /**
   * {@inheritdoc}
   */
  public function allowed(WorkflowTransition $transition, WorkflowInterface $workflow, EntityInterface $entity) {
    // @todo Add an additional condition here that makes sense for tests.
    if ($transition->getId() == 'fulfill') {
      return FALSE;
    }
  }

}
