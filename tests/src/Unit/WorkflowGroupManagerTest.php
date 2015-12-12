<?php

/**
 * @file
 * Contains \Drupal\Tests\state_machine\Unit\WorkflowGroupManagerTest.
 */

namespace Drupal\Tests\state_machine\Unit;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \Drupal\state_machine\WorkflowGroupManager
 * @group Workflow
 */
class WorkflowGroupManagerTest extends WorkflowBaseTestCase {

  /**
   * The group manager under test.
   *
   * @var \Drupal\Tests\state_machine\Unit\TestWorkflowGroupManager
   */
  protected $groupManager;

  /**
   * The expected definitions array.
   *
   * @var array
   */
  protected $expectedDefinitions = [
    'order' => [
      'id' => 'order',
      'label' => 'Order',
      'entity_type' => 'commerce_order',
      'class' => 'Drupal\state_machine\Plugin\WorkflowGroup\WorkflowGroup',
      'workflow_class' => '\Drupal\state_machine\Plugin\Workflow\Workflow',
      'provider' => 'state_machine_test',
    ]
  ];

  /**
   * Provide a set of invalid config workflow groups to test the process
   * definitions.
   */
  public function invalidConfigWorkflowGroups() {
    return [
      [['workflow_group_1' => [
        'entity_type' => 'commerce_order',
      ]]],
      [['workflow_group_2' => [
        'label' => 'order',
      ]]],
    ];
  }

  /**
   * Tests the processDefinition method with missing keys.
   *
   * @param $group_config array
   *  Workflow group configuration that will be translated into YAML.
   *
   * @covers ::processDefinition
   * @dataProvider invalidConfigWorkflowGroups
   */
  public function testProcessInvalidDefinitions($group_config) {
    vfsStream::setup('root');
    $file = Yaml::encode($group_config);
    vfsStream::create([
        'state_machine_test' => [
          'state_machine_test.workflow_groups.yml' => $file,
        ]]
    );

    $discovery = new YamlDiscovery('workflow_groups', ['state_machine_test' => vfsStream::url('root/state_machine_test')]);
    $this->groupManager->setDiscovery($discovery);
    $required_properties = ['label', 'entity_type'];

    $definition = $discovery->getDefinitions();
    $missing_properties = array_diff($required_properties, array_keys($group_config));
    $this->setExpectedException('Drupal\Component\Plugin\Exception\PluginException',
      sprintf('The workflow_group %s must define the %s property.', key($definition), reset($missing_properties)));
    $this->groupManager->processDefinition($definition, key($definition));
  }

  /**
   * @covers: getDefinitionsByEntityType
   */
  public function testGetDefinitionsByEntityType() {
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
    $this->assertEquals($this->expectedDefinitions, $this->groupManager->getDefinitionsByEntityType('commerce_order'), 'Workflow group definition matches the expectations');
  }

}
