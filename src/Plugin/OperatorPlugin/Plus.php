<?php

namespace Drupal\mathd8\Plugin\OperatorPlugin;

use Drupal\mathd8\Plugin\OperatorPluginBase;

/**
 * Plugin implementation of .
 *
 * @OperatorPlugin(
 *   id = "+",
 *   label = @Translation("Plus"),
 *   module = "mathd8",
 *   description = @Translation("Sum operator"),
 *   symbol = "+",
 *   precedence = 2
 * )
 */
class Plus extends OperatorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate($op1, $op2) {
    return $op1 + $op2;
  }

}
