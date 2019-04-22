<?php

namespace Drupal\mathd8\Plugin\OperatorPlugin;

use Drupal\mathd8\Plugin\OperatorPluginBase;
use Drupal\mathd8\Exception\MalformedExpressionException;

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
    if ($op2 != 0) {
      return $op1 / $op2;
    }
    else {
      throw new MalformedExpressionException("Arithmetic Exception: division by zero");
    }
  }

}
