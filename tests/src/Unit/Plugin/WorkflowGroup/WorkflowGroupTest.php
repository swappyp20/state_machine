<?php

/**
 * @file
 * Contains \Drupal\Tests\state_machine\Unit\WorkflowGroupTest.
 */

namespace Drupal\Tests\state_machine\Unit;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \Drupal\state_machine\Plugin\WorkflowGroup\WorkflowGroup
 * @group Workflow
 */
class WorkflowGroupTest extends WorkflowBaseTestCase {

  /**
   * Tests the group with a config YAML file
   *
   * @covers ::getLabel
   * @covers ::getEntityTypeId
   * @covers ::getWorkflowClass
   */
  public function testProcessValidDefinition() {
    vfsStream::setup('root');
    $group_config = [
      'order' => [
        'label' => 'Order',
        'entity_type' => 'commerce_order',
      ]
    ];
    $file = Yaml::encode($group_config);
    vfsStream::create([
        'state_machine_test' => [
          'state_machine_test.workflow_groups.yml' => $file,
        ]]
    );

    $discovery = new YamlDiscovery('workflow_groups', ['state_machine_test' => vfsStream::url('root/state_machine_test')]);
    $this->groupManager->setDiscovery($discovery);

    /** @var $workflow_group \Drupal\state_machine\Plugin\WorkflowGroup\WorkflowGroup */
    $workflow_group = $this->groupManager->createInstance('order');
    $this->assertEquals('Order', $workflow_group->getLabel(), 'Workflow group label matches the expected one');
    $this->assertEquals('commerce_order', $workflow_group->getEntityTypeId(), 'Workflow group entity type id matches the expected one');
    $this->assertEquals('\Drupal\state_machine\Plugin\Workflow\Workflow', $workflow_group->getWorkflowClass(), 'Workflow group class matches the expected one');
  }

}
