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

}
