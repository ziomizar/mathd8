<?php

namespace Drupal\mathd8\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Operator plugin plugin manager.
 */
class OperatorPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new OperatorPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/OperatorPlugin', $namespaces, $module_handler, 'Drupal\mathd8\Plugin\OperatorPluginInterface', 'Drupal\mathd8\Annotation\OperatorPlugin');

    $this->alterInfo('mathd8_operator_manager_info');
    $this->setCacheBackend($cache_backend, 'mathd8_operator_manager_plugins');
  }

  /**
   * Load all the operators available.
   */
  public function loadAllOperators() {
    $instances = [];
    foreach ($this->getDefinitions() as $plugin) {
      $instances[$plugin['id']] = $this->createInstance($plugin['id']);
    }

    return $instances;
  }

}
