<?php

namespace Drupal\mathd8\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for Operator plugin plugins.
 */
abstract class OperatorPluginBase extends PluginBase implements OperatorPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function precedence() {
    return $this->pluginDefinition['precedence'];
  }

  /**
   * {@inheritdoc}
   */
  public function symbol() {
    return $this->pluginDefinition['symbol'];
  }

  /**
   * {@inheritdoc}
   */
  abstract public function evaluate($op1, $op2);

}
