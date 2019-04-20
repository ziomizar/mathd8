<?php

namespace Drupal\mathd8\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Operator plugin plugins.
 */
interface OperatorPluginInterface extends PluginInspectionInterface {

  /**
   * Get the precedence of an operator.
   *
   * @return int
   *   The precedence of the operator.
   */
  public function precedence();

  /**
   * Get the symbol of the operator.
   *
   * @return string
   *   The symbol of the operator.
   */
  public function symbol();

  /**
   * Evaluate the expression related to the symbol.
   *
   * @param mixed $op1
   *   The first operand of the expression.
   * @param mixed $op2
   *   The second operand of the expression.
   *
   * @return mixed
   *   The result of the evaluation.
   */
  public function evaluate($op1, $op2);

}
