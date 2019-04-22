<?php

namespace Drupal\mathd8;

/**
 * Interface ParserInterface.
 */
interface ParserInterface {

  /**
   * Constructs a new Mathd8Converter object.
   *
   * @param LexerInterface $lexer
   *   The Lexer service.
   */
  public function __construct(LexerInterface $lexer);

  /**
   * Get all the tokens of the expression.
   *
   * @return array
   *   The list of tokens.
   */
  public function expression();

  /**
   * Return all steps performed to parse the expression.
   *
   * @return array
   *   An array of steps.
   */
  public function steps();

  /**
   * Evaluate a string containing a mathematical expression.
   *
   * @param string $expression
   *   The string with the expression.
   *
   * @return \Drupal\mathd8\Controller\Token
   *   The token containing the result of the expression.
   */
  public function evaluate($expression);

  /**
   * Validate the mathematical expression.
   *
   * @param string $expression
   *   The mathematical expression.
   *
   * @throws \Drupal\mathd8\Exception\InvalidTokenException.
   *   In case has been used an invalid token in the expression.
   * @throws \Drupal\mathd8\Exception\MalformedExpressionException.
   *   In case has been used an invalid token in the expression.
   *
   * @return bool
   *   TRUE if a valid expression
   *   FALSE if contain invalid tokens.
   */
  public function validateExpression($expression);

  /**
   * Evaluate the text and return an array with all the operations.
   *
   * @params string $expression
   *   The mathematical expression.
   *
   * @return array
   *   Array with result, steps and tokens.
   */
  public function getEvaluationSteps($expression);

}
