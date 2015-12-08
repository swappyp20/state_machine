<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\Plugin\Field\FieldType\StateItemInterface.
 */

namespace Drupal\commerce_workflow\Plugin\Field\FieldType;

use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * Defines the interface for state item fields.
 */
interface StateItemInterface extends OptionsProviderInterface {

  /**
   * Gets the workflow used by the current field.
   *
   * @return \Drupal\commerce_workflow\Plugin\Workflow\WorkflowInterface|false
   *   The workflow, or FALSE if unknown at this time.
   */
  public function getWorkflow();

  /**
   * Gets whether the current state is valid.
   *
   * Drupal separates field validation into a separate step, allowing an
   * invalid state to be set before validation is invoked. At that point
   * validation has no access to the previous value, so it can't determine
   * if the transition is allowed. Thus, the field item must track the state
   * changes internally, and answer via this method if the current state is
   * valid.
   *
   * @see \Drupal\commerce_workflow\Plugin\Validation\Constraint\StateConstraintValidator
   *
   * @return bool
   *   TRUE if the current state is valid, FALSE otherwise.
   */
  public function isValid();

}
