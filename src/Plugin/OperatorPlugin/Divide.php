<?php

namespace Drupal\mathd8\Plugin\OperatorPlugin;

use Drupal\mathd8\Plugin\OperatorPluginBase;

/**
 * Plugin implementation of .
 *
 * @OperatorPlugin(
 *   id = "/",
 *   label = @Translation("Divide"),
 *   module = "mathd8",
 *   description = @Translation("Divide operator"),
 *   symbol = "/",
 *   precedence = 3
 * )
 */
class Divide extends OperatorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate($op1, $op2) {
    return $op1 / $op2;
  }

}
