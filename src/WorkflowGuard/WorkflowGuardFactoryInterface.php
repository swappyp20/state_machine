<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\WorkflowGuard\WorkflowGuardFactoryInterface.
 */

namespace Drupal\commerce_workflow\WorkflowGuard;

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
   * @return \Drupal\commerce_workflow\WorkflowGuard\WorkflowGuardInterface[]
   */
  public function get($group_id);

}
