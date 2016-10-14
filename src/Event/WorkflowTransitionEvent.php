<?php

namespace Drupal\state_machine\Event;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowState;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the state change event.
 */
class WorkflowTransitionEvent extends Event {

  /**
   * The workflow state.
   *
   * @var \Drupal\state_machine\Plugin\Workflow\WorkflowState
   */
  protected $fromState;

  /**
   * The workflow state.
   *
   * @var \Drupal\state_machine\Plugin\Workflow\WorkflowState
   */
  protected $toState;

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entity;

  /**
   * Constructs a new StateChangeEvent.
   *
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowState $from
   *   The initial workflow state.
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowState $to
   *   The final workflow state.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   */
  public function __construct(WorkflowState $from, WorkflowState $to, ContentEntityInterface $entity) {
    $this->fromState = $from;
    $this->toState = $to;
    $this->entity = $entity;
  }

  /**
   * Gets the state transitioned from.
   *
   * @return \Drupal\state_machine\Plugin\Workflow\WorkflowState
   *   The workflow state.
   */
  public function getFromState() {
    return $this->fromState;
  }

  /**
   * Gets the state transitioned to.
   *
   * @return \Drupal\state_machine\Plugin\Workflow\WorkflowState
   *   The workflow state.
   */
  public function getToState() {
    return $this->toState;
  }

  /**
   * Gets the entity.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   The entity.
   */
  public function getEntity() {
    return $this->entity;
  }

}
