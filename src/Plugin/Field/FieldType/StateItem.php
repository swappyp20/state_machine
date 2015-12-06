<?php

/**
 * @file
 * Contains \Drupal\commerce_workflow\Plugin\Field\FieldType\StateItem.
 */

namespace Drupal\commerce_workflow\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslationWrapper;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * Plugin implementation of the 'state' field type.
 *
 * @FieldType(
 *   id = "state",
 *   label = @Translation("State"),
 *   description = @Translation("Stores the current workflow state."),
 *   default_widget = "options_select",
 *   default_formatter = "list_default"
 * )
 */
class StateItem extends FieldItemBase implements OptionsProviderInterface {

  /**
   * A cache of loaded workflows, keyed by field definition hash.
   *
   * @var array
   */
  protected static $workflows = [];

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar_ascii',
          'length' => 255,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslationWrapper('State'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'workflow' => '',
      'workflow_callback' => '',
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];
    // Allow the workflow to be changed if it's not determined by a callback.
    if (!$this->getSetting('workflow_callback')) {
      $workflow_manager = \Drupal::service('plugin.manager.workflow');
      $workflows = $workflow_manager->getGroupedLabels($this->getEntity()->getEntityTypeId());

      $element['workflow'] = [
        '#type' => 'select',
        '#title' => $this->t('Workflow'),
        '#options' => $workflows,
        '#default_value' => $this->getSetting('workflow'),
        '#required' => TRUE,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return $this->value === NULL || $this->value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function applyDefaultValue($notify = TRUE) {
    if ($workflow = $this->getWorkflow()) {
      $initial_state = reset($workflow->getStates());
      $this->setValue(['value' => $initial_state->getId()], $notify);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return array_keys($this->getPossibleOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $workflow = $this->getWorkflow();
    if (!$workflow) {
      // The workflow is not known yet, the field is probably being created.
      return [];
    }
    $state_labels = array_map(function ($state) {
      return $state->getLabel();
    }, $workflow->getStates());

    return $state_labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return array_keys($this->getSettableOptions($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $workflow = $this->getWorkflow();
    if (!$workflow) {
      // The workflow is not known yet, the field is probably being created.
      return [];
    }
    $entity = $this->getEntity();
    // $this->value is unpopulated due to https://www.drupal.org/node/2629932
    $field_name = $this->getFieldDefinition()->getName();
    $value = $entity->get($field_name)->value;

    $state_labels = [
      // The current state is always allowed.
      $value => $workflow->getState($value)->getLabel(),
    ];
    $transitions = $workflow->getAllowedTransitions($value, $entity);
    foreach ($transitions as $transition) {
      $state = $transition->getToState();
      $state_labels[$state->getId()] = $state->getLabel();
    }

    return $state_labels;
  }

  /**
   * Gets the workflow used by the current field.
   *
   * @return \Drupal\commerce_workflow\Plugin\Workflow\WorkflowInterface|false
   *   The workflow, or FALSE if unknown at this time.
   */
  protected function getWorkflow() {
    $field_definition = $this->getFieldDefinition();
    $definition_id = spl_object_hash($field_definition);
    if (!isset(static::$workflows[$definition_id])) {
      static::$workflows[$definition_id] = $this->loadWorkflow();
    }

    return static::$workflows[$definition_id];
  }

  /**
   * Loads the workflow used by the current field.
   *
   * @return \Drupal\commerce_workflow\Plugin\Workflow\WorkflowInterface|false
   *   The workflow, or FALSE if unknown at this time.
   */
  protected function loadWorkflow() {
    $workflow = FALSE;
    if ($callback = $this->getSetting('workflow_callback')) {
      $workflow = call_user_func($callback, $this->getEntity());
      if (!$workflow) {
        throw new \RuntimeException(sprintf('%s did not return a workflow.', $callback));
      }
    }
    elseif ($workflow_id = $this->getSetting('workflow')) {
      $workflow_manager = \Drupal::service('plugin.manager.workflow');
      $workflow = $workflow_manager->createInstance($workflow_id);
    }

    return $workflow;
  }

}
