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
   * @param Drupal\mathd8\Plugin\OperatorPluginManager $operatorManager
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

}
