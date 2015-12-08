<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\Plugin\Validation\Constraint\StateConstraintValidator.
 */

namespace Drupal\commerce_workflow\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the State constraint.
 *
 * @see \Drupal\commerce_workflow\Plugin\Field\FieldType\StateItemInterface::isValid()
 */
class StateConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (!$value->getEntity()->isNew() && !$value->isValid()) {
      $this->context->addViolation($constraint->message, ['@state' => $value->value]);
    }
  }

}
