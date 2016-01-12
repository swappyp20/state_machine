<?php

/**
 * @file
 * Contains \Drupal\state_machine\Plugin\views\filter\State.
 */

namespace Drupal\state_machine\Plugin\views\filter;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\filter\InOperator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter by workflow state.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("state_machine_state")
 */
class State extends InOperator {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Constructs a new State object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      // Merge the states of all workflows into one list, preserving their
      // initial positions.
      $states = [];
      foreach ($this->getWorkflows() as $workflow) {
        $weight = 0;
        foreach ($workflow->getStates() as $state_id => $state) {
          $states[$state_id] = [
            'label' => $state->getLabel(),
            'weight' => $weight,
          ];
          $weight++;
        }
      }
      uasort($states, array('Drupal\Component\Utility\SortArray', 'sortByWeightElement'));

      $this->valueOptions = array_map(function ($state) {
        return $state['label'];
      }, $states);
    }

    return $this->valueOptions;
  }

  /**
   * Gets the workflows used by the current field.
   *
   * @return \Drupal\state_machine\Plugin\Workflow\WorkflowInterface[]
   *   The workflows.
   */
  protected function getWorkflows() {
    // Only the StoreItem knows which workflow it's using. This requires us
    // to create an entity for each bundle in order to get the store field.
    $entity_type_id = $this->getEntityType();
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $bundles = $this->getBundles($entity_type);
    $storage = $this->entityTypeManager->getStorage($entity_type_id);
    $workflows = [];
    // The name of the entity field is stored in different places for
    // configurable and base fields.
    if (isset($this->configuration['field_name'])) {
      $field_name = $this->configuration['field_name'];
    }
    else {
      $field_name = $this->configuration['entity field'];
    }

    foreach ($bundles as $bundle) {
      $values = [];
      if ($bundle_key = $entity_type->getKey('bundle')) {
        $values[$bundle_key] = $bundle;
      }
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $storage->create($values);
      if ($entity->hasField($field_name)) {
        $workflow = $entity->get($field_name)->first()->getWorkflow();
        $workflows[$workflow->getId()] = $workflow;
      }
    }

    return $workflows;
  }

  /**
   * Gets the bundles for the current entity type.
   *
   * If the view has a non-exposed bundle filter, the bundles are taken from
   * there. Otherwise, the full bundle list is used.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The current entity type.
   *
   * @return string[]
   *   The bundles.
   */
  protected function getBundles(EntityTypeInterface $entity_type) {
    $bundles = [];
    $bundle_key = $entity_type->getKey('bundle');
    if ($bundle_key && isset($this->view->filter[$bundle_key])) {
      $filter = $this->view->filter[$bundle_key];
      if (!$filter->isExposed() && !empty($filter->value)) {
        // 'all' is added by Views and isn't a bundle.
        $bundles = array_diff(['all'], $filter->value);
      }
    }
    // Fallback to the list of all bundles.
    if (empty($bundles)) {
      $bundles = array_keys($this->entityTypeBundleInfo->getBundleInfo($entity_type->id()));
    }

    return $bundles;
  }

}
