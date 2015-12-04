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
  public function getPossibleValues(AccountInterface $account = NULL) {
    $states = $this->getWorkflow()->getStates();
    return array_keys($states);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $states = $this->getWorkflow()->getStates();
    $state_labels = array_map(function ($state) {
      return $state->getLabel();
    }, $states);
    return $state_labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    $states = $this->getWorkflow()->getStates();
    return array_keys($states);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    $states = $this->getWorkflow()->getStates();
    $state_labels = array_map(function ($state) {
      return $state->getLabel();
    }, $states);
    return $state_labels;
  }

  /**
   * Gets the workflow used by the current field.
   *
   * @return \Drupal\commerce_workflow\Plugin\Workflow\WorkflowInterface
   *   The workflow.
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
   * @return \Drupal\commerce_workflow\Plugin\Workflow\WorkflowInterface
   *   The workflow.
   */
  protected function loadWorkflow() {
    if ($callback = $this->getSetting('workflow_callback')) {
      $workflow = call_user_func($callback, $this->getEntity());
      if (!$workflow) {
        throw new \RuntimeException(sprintf('%s did not return a workflow.', $callback));
      }
    }
    else {
      $workflow_manager = \Drupal::service('plugin.manager.workflow');
      $workflow = $workflow_manager->createInstance($this->getSetting('workflow'));
    }

    return $workflow;
  }

}
