<?php

namespace Drupal\Tests\state_machine\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the StateChangeEvent.
 *
 * @group state_machine
 */
class WorkflowTransitionEventTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_test',
    'state_machine',
    'field',
    'user',
    'state_machine_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('entity_test');
    $this->installEntitySchema('user');

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'test_state',
      'entity_type' => 'entity_test',
      'type' => 'state',
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_name' => 'test_state',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
      'settings' => [
        'workflow' => 'default',
      ],
    ]);
    $field->save();
  }

  /**
   * Tests the state change event dispatch and test subscriber.
   */
  public function testStateChangeEvent() {
    $entity = EntityTest::create([
      'name' => 'Tester',
      'test_state' => ['value' => 'new'],
    ]);
    $entity->save();

    /** @var \Drupal\state_machine\WorkflowManagerInterface $workflow_manager */
    $workflow_manager = \Drupal::service('plugin.manager.workflow');
    /** @var \Drupal\state_machine\Plugin\Workflow\Workflow $workflow */
    $workflow = $workflow_manager->createInstance('default');
    $transition = $workflow->getTransition('cancel');
    $entity->test_state->first()->applyTransition($transition);
    $entity->save();

    $messages = drupal_get_messages();
    $message = reset($messages);
    $this->assertEquals('Tester was Canceled at Pre-transition.', (string) $message[0]);
    $this->assertEquals('Tester was Canceled at Post-transition.', (string) $message[1]);
  }

}
