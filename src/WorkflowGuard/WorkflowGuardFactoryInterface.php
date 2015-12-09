<?php

/**
 * @file
 * Contains \Drupal\state_machine\WorkflowGuard\WorkflowGuardFactoryInterface.
 */

namespace Drupal\state_machine\WorkflowGuard;

/**
 * Defines the interface for workflow guard factories.
 */
interface WorkflowGuardFactoryInterface {

 /**
   * Gets the instantiated workflow guards for the given group id.
   *
   * @param string $group_id
   *   The group id.
   *
   * @return \Drupal\state_machine\WorkflowGuard\WorkflowGuardInterface[]
   */
  public function get($group_id);

}
