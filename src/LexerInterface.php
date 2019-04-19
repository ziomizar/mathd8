<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Plugin\OperatorPluginManager;

/**
 * Interface LexerInterface.
 */
interface LexerInterface {

  /**
   * Constructs a new Lexer object.
   *
   * @param \Drupal\mathd8\Plugin\OperatorPluginManager $operatorManager
   *   The operator plugin manager.
   */
  public function __construct(OperatorPluginManager $operatorManager);

  /**
   * Return the tokens in the original infix notation.
   *
   * @return array
   *   Array of tokens.
   */
  public function getInfixTokens();

  /**
   * Split a mathematical expression in tokens.
   *
   * @param string $expression
   *   The mathematical expression.
   *
   * @return array
   *   Array of token objects.
   */
  public function getTokens($expression);

  /**
   * Return all the operators plugins.
   *
   * @return array
   *   Return an array of operators plugins.
   */
  public function getOperators();

  /**
   * Get the regular expression to match operands.
   *
   * @return string
   *   The regular expression.
   */
  public function getOperandsRegex();

  /**
   * Get the regular expression to match operators.
   *
   * @return string
   *   The regular expression.
   */
  public function getOperatorsRegex();

  /**
   * Build the regexp used to define all the tokens in the expression.
   *
   * @return string
   *   The regular expression with all the valid tokens.
   */
  public function getAllRegex();

  /**
   * Helper method to check if a token is an operator.
   *
   * @param string $op
   *   The operator to test.
   *
   * @return bool
   *   TRUE if is an operator
   *   FALSE otherwise
   */
  public function isOperator($op);

  /**
   * Helper method to check if a token is an operator.
   *
   * @param string $op
   *   The operator to test.
   *
   * @return bool
   *   TRUE if is an operator
   *   FALSE otherwise
   */
  public function isOperand($op);

  /**
   * Helper method to check if is a valid token.
   *
   * @param string $op
   *   The token to test.
   *
   * @return bool
   *   TRUE if is an operator or an operand
   *   FALSE otherwise
   */
  public function isValid($op);

}
