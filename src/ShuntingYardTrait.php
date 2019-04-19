<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;
use Drupal\mathd8\Exception\InvalidTokenException;

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
    try {
      $tokens = $this->lexer->getTokens($expression);
    }
    catch (InvalidTokenException $e) {
      $tokens = [];
    }

    $this->outputQueue = $this->operatorStack = [];

    foreach ($tokens as $key => $token) {
      if ($this->lexer->isOperand($token->value())) {
        // Add the operand to the output queue.
        $this->addToOutput($token);
      }
      elseif ($this->lexer->isOperator($token->value())) {
        // Pop all the operators with higher prio from the stack.
        if (is_array($this->operatorStack)) {
          foreach ($this->operatorStack as $op_in_stack) {
            // The operator has already been checked and pushed,
            // so for sure is defined.
            $operator_in_stack = $this->operators[$op_in_stack->value()];
            $operator = $this->operators[$token->value()];
            if ($operator_in_stack->precedence() >= $operator->precedence()) {
              // The operator in stack have higher precedence than the current
              // token. Remove it from the operator stack and return its value.
              $this->addToOutput($this->extractOperator());
            }
          }
        }
        // When all the operators in the stack with higher precedence has been
        // evaluated add the current operator in the stack.
        $this->addOperator($token);
      }
    }

    // If there are still operator token on the stack put all of them in the
    // output queue.
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
