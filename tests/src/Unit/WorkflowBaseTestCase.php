<?php

/**
 * @file
 * Contains \Drupal\Tests\state_machine\Unit\WorkflowBaseTest.
 */

namespace Drupal\Tests\state_machine\Unit;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\state_machine\WorkflowGroupManager;
use Drupal\Tests\UnitTestCase;

class WorkflowBaseTestCase extends UnitTestCase {

  /**
   * The cache backend to use.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $cache;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The plugin discovery.
   *
   * @var \Drupal\Component\Plugin\Discovery\DiscoveryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $discovery;

  /**
   * The group manager under test.
   *
   * @var \Drupal\Tests\state_machine\Unit\TestWorkflowGroupManager
   */
  protected $groupManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Prepare the default constructor arguments required by
    // WorkflowGroupManager.
    $this->cache = $this->getMock('Drupal\Core\Cache\CacheBackendInterface');
    $this->moduleHandler = $this->prophesize(ModuleHandlerInterface::class);
    $this->moduleHandler->moduleExists('state_machine_test')->willReturn(TRUE);
    $this->groupManager = new TestWorkflowGroupManager($this->moduleHandler->reveal(), $this->cache);
  }

}

/**
 * Provides a testing version of WorkflowGroupManager with an empty constructor.
 */
class TestWorkflowGroupManager extends WorkflowGroupManager {
  /**
   * Sets the discovery for the manager.
   *
   * @param \Drupal\Component\Plugin\Discovery\DiscoveryInterface $discovery
   *   The discovery object.
   */
  public function setDiscovery(DiscoveryInterface $discovery) {
    $this->discovery = $discovery;
  }

}
