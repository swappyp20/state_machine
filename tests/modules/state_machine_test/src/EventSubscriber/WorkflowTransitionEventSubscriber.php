<?php

namespace Drupal\state_machine_test\EventSubscriber;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Test subscriber to state changes.
 */
class WorkflowTransitionEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'entity_test.cancel.pre_transition' => 'cancelPresaveReaction',
      'entity_test.cancel.post_transition' => 'cancelPostsaveReaction',
    ];
    return $events;
  }

  /**
   * Reacts to entity entering cancel state in the PreSave phase.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The state change event.
   */
  public function cancelPresaveReaction(WorkflowTransitionEvent $event) {
    $state = $event->getToState();
    $entity = $event->getEntity();
    drupal_set_message(new TranslatableMarkup('@entity_label was @state_label at Pre-transition.', [
      '@entity_label' => $entity->label(),
      '@state_label' => $state->getLabel(),
    ]));
  }

  /**
   * Reacts to entity entering cancel state in the PreSave phase.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The state change event.
   */
  public function cancelPostsaveReaction(WorkflowTransitionEvent $event) {
    $state = $event->getToState();
    $entity = $event->getEntity();
    drupal_set_message(new TranslatableMarkup('@entity_label was @state_label at Post-transition.', [
      '@entity_label' => $entity->label(),
      '@state_label' => $state->getLabel(),
    ]));
  }

}
