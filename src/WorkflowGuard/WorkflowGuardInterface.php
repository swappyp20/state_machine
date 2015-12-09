<?php

/**
 * @file
 * Contains \Drupal\state_machine\WorkflowGuard\WorkflowGuardInterface.
 */

namespace Drupal\state_machine\WorkflowGuard;

use Drupal\state_machine\Plugin\Workflow\WorkflowInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowTransition;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the interface for workflow guards.
 *
 * Allows for custom logic controling the availability of specific transitions.
 * Transitions could be restricted based on the current user's permissions, a
 * parent entity field, etc.
 *
 * By default, a transition is allowed unless at least one guard returns FALSE.
 */
interface WorkflowGuardInterface {

  /**
   * Checks whether the given transition is allowed.
   *
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowTransition $transition
   *   The transition.
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowInterface $workflow
   *   The workflow.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The parent entity.
   *
   * @return bool
   *   TRUE if the transition is allowed, FALSE otherwise.
   */
  public function allowed(WorkflowTransition $transition, WorkflowInterface $workflow, EntityInterface $entity);

}
