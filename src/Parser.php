<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;
use Drupal\mathd8\Exception\InvalidTokenException;
use Drupal\mathd8\Exception\MalformedExpressionException;

/**
 * Class Parser.
 */
class Parser implements ParserInterface {

  use ShuntingYardTrait;

  /**
   * The operator manager service.
   *
   * @var \Drupal\mathd8\Lexer
   */
  protected $lexer;

  /**
   * The stack used to compute the postfix expression.
   *
   * @var array
   */
  protected $stack;

  /**
   * An array of tokens from the original expression in infix order.
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
   * Push a new Token in the stack.
   *
   * @params \Drupal\mathd8\Controller\Token $op
   *   A Token objects.
   */
  private function push(Token $op) {
    if ($op) {
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
    if (!$this->validateExpression($expression)) {
      // The expression is invalid because contain invalid tokens.
      throw new InvalidTokenException("The expression contain invalid tokens");
    }

    // Get the expression is postfix format.
    /** @var \Drupal\mathd8\Controller\Token[] $expr */
    $expr = $this->toPostfix($expression);
    // Get the original infix format of the expression.
    /** @var \Drupal\mathd8\Controller\Token[] $tokens */
    $this->tokens = $this->lexer->getInfixTokens();
    // Reset the step list.
    $this->steps = [];

    foreach ($expr as $key => $token) {
      if ($this->lexer->isOperator($token->value())) {
        // Check if at least two operands are already in the stack.
        if (count($this->stack) < 2) {
          throw new MalformedExpressionException("Invalid order of operands and operators");
        }

        $op2 = $this->pop();
        $op1 = $this->pop();
        $operator = $this->operators[$token->value()];

        try {
          $result = $operator->evaluate($op1->value(), $op2->value());
        }
        catch (MalformedExpressionException $e) {
          // Reset the steps array as any operation should be done.
          $this->steps = [];
          $this->steps[] = [
            'op1' => $op1->position(),
            'op2' => $op2->position(),
            'operator' => $token->position(),
            'result_value' => NULL,
            'result' => $e->getMessage(),
          ];

          return new Token(0, 0);
        }
        // Save the result of this operation as a token with a custom position
        // used as id.
        $result = new Token($result, 'result-step-' . count($this->steps));
        $this->push($result);

        // A step is composed by:
        // - the positions/id of all the tokens interested in an evaluation.
        // - the result of the evaluation.
        $this->steps[] = [
          'op1' => $op1->position(),
          'op2' => $op2->position(),
          'operator' => $token->position(),
          'result_value' => $result->value(),
          'result' => $result->position(),
        ];
      }
      elseif ($this->lexer->isOperand($token->value())) {
        $this->push($token);
      }
      else {
        throw new InvalidTokenException(sprintf("Token %s is not a valid token.", $token->value()));
      }
    }

    return $this->pop();
  }

  /**
   * {@inheritdoc}
   */
  public function validateExpression($expression) {
    if (!$this->lexer->isValidExpression($expression) || !$this->lexer->haveInvalidTokens($expression)) {
      return FALSE;
    }
    return TRUE;
  }

}
