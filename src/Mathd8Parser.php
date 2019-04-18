<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;

/**
 * Class Mathd8Parser.
 */
class Mathd8Parser implements Mathd8ParserInterface {

  use ShuntingYardTrait;

  /**
   * The operator manager service.
   *
   * @var \Drupal\mathd8\Lexer
   */
  protected $lexer;

  /**
   * The stack used to compute the expression.
   *
   * @var array
   */
  protected $stack;

  /**
   * An array of tokens.
   *
   * @var array
   */
  protected $tokens;

  /**
   * An array of steps.
   *
   * @var array
   */
  protected $steps;

  /**
   * An array of operators.
   *
   * @var array
   */
  protected $operators;

  /**
   * {@inheritdoc}
   */
  public function __construct(LexerInterface $lexer) {
    $this->lexer = $lexer;
    $this->stack = [];
    $this->steps = [];
    $this->operators = $this->lexer->getOperators();
  }

  /**
   * Get the expression as an array of Token.
   *
   * @params \Drupal\mathd8\Controller\Token
   *   A Token objects.
   */
  private function push(Token $op) {
    if ($op) {
      // TODO: check better if is numeric or a valid operator.
      array_push($this->stack, $op);
    }
  }

  /**
   * Get a token from the stack.
   *
   * @return \Drupal\mathd8\Controller\Token
   *   The last token in the stack.
   */
  private function pop() {
    return array_pop($this->stack);
  }

  /**
   * {@inheritdoc}
   */
  public function expression() {
    return $this->tokens;
  }

  /**
   * {@inheritdoc}
   */
  public function steps() {
    return $this->steps;
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate($expression) {

    $expr = $this->toPostfix($expression);
    $this->tokens = $this->lexer->getInfixTokens();
    // Reset the step list.
    $this->steps = [];

    foreach ($expr as $key => $token) {
      if (isset($this->operators[$token->value()])) {
        // TODO: check if there are 2 operands in stack.
        $op2 = $this->pop();
        $op1 = $this->pop();

        $result = $this->operators[$token->value()]->evaluate($op1->value(), $op2->value());
        $result = new Token($result, 'result-step-' . count($this->steps));
        $this->push($result);

        $this->steps[] = [
          'op1' => $op1->position(),
          'op2' => $op2->position(),
          'operator' => $token->position(),
          'result_value' => $result->value(),
          'result' => $result->position(),
        ];
      }
      elseif (is_numeric($token->value())) {
        $this->push($token);
      }
      else {
        // TODO: Exception.
      }
    }

    return $this->pop();
  }

}
