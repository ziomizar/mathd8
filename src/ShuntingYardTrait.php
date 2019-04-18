<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;

/**
 * Class ShuntingYardTrait.
 *
 * Parsing mathematical expressions specified in infix notation.
 * It produce a postfix notation array, also known as
 * Reverse Polish notation (RPN).
 */
trait ShuntingYardTrait {

  /**
   * The operator stack.
   *
   * @var array
   */
  protected $operatorStack;

  /**
   * The output queue used to save results of computations.
   *
   * @var array
   */
  protected $outputQueue;

  /**
   * Parse mathematical expressions specified in infix notation.
   *
   * It use the shunting-yard algorithm. It produce a postfix notation string,
   * also known as Reverse Polish notation (RPN).
   *
   * @param string $expression
   *   A string with a mathematical expression in infix notation.
   *
   * @return array
   *   The list of tokens reordered in RPN.
   *
   * @see https://en.wikipedia.org/wiki/Shunting-yard_algorithm
   */
  public function toPostfix($expression) {

    $tokens = $this->lexer->getTokens($expression);
    $this->outputQueue = $this->operatorStack = [];

    foreach ($tokens as $key => $token) {
      if (is_numeric($token->value())) {
        $this->addToOutput($token);
      }
      if (array_key_exists($token->value(), $this->operators)) {
        // Pop all the operators with higher prio from the stack.
        if (is_array($this->operatorStack)) {
          foreach ($this->operatorStack as $op_in_stack) {
            // The operator has already been checked and pushed,
            // so for sure is defined.
            if ($this->operators[$op_in_stack->value()]->precedence() >= $this->operators[$token->value()]->precedence()) {
              $this->addToOutput($this->extractOperator());
            }
          }
        }
        // When all the oeprators with more prio in the stack
        // has been evaluated add this operator.
        $this->addOperator($token);
      }
    }

    // If there are still operator token on the stack.
    while (count($this->operatorStack)) {
      $this->addToOutput($this->extractOperator());
    }

    return $this->outputQueue;
  }

  /**
   * Add an operator to the operator stack.
   *
   * @params \Drupal\mathd8\Controller\Token $op
   *   The token object.
   */
  protected function addOperator(Token $op) {
    array_push($this->operatorStack, $op);
  }

  /**
   * Remove and return an operator from the oprerator stack.
   *
   * @return \Drupal\mathd8\Controller\Token
   *   The Token object.
   */
  protected function extractOperator() {
    return array_pop($this->operatorStack);
  }

  /**
   * Add an operand to the output stack.
   *
   * @params \Drupal\mathd8\Controller\Token $op
   *   The token object.
   */
  protected function addToOutput(Token $op) {
    array_push($this->outputQueue, $op);
  }

}
