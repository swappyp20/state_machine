<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\Plugin\Validation\Constraint\StateConstraint.
 */

namespace Drupal\commerce_workflow\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensures the validity of the specified state.
 *
 * The state must exist on the used workflow, and be in the allowed transitions.
 *
 * @Constraint(
 *   id = "State",
 *   label = @Translation("State", context = "Validation")
 * )
 */
class StateConstraint extends Constraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = "The state '@state' is invalid.";

}
