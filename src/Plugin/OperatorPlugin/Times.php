<?php

namespace Drupal\mathd8\Plugin\OperatorPlugin;

use Drupal\mathd8\Plugin\OperatorPluginBase;

/**
 * Plugin implementation of .
 *
 * @OperatorPlugin(
 *   id = "*",
 *   label = @Translation("Times"),
 *   module = "mathd8",
 *   description = @Translation("Times operator"),
 *   symbol = "*",
 *   precedence = 3
 * )
 */
class Times extends OperatorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate($op1, $op2) {
    return $op1 * $op2;
  }

}
