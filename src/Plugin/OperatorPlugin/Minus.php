<?php

namespace Drupal\mathd8\Plugin\OperatorPlugin;

use Drupal\mathd8\Plugin\OperatorPluginBase;

/**
 * Plugin implementation of .
 *
 * @OperatorPlugin(
 *   id = "-",
 *   label = @Translation("Minus"),
 *   module = "mathd8",
 *   description = @Translation("Minus operator"),
 *   symbol = "-",
 *   precedence = 2
 * )
 */
class Minus extends OperatorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate($op1, $op2) {
    return $op1 - $op2;
  }

}
