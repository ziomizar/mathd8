<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;
use Drupal\mathd8\Plugin\OperatorPluginManager;
use Drupal\mathd8\Exception\InvalidTokenException;
use Drupal\mathd8\Exception\MalformedExpressionException;

/**
 * Class Lexer.
 */
class Lexer implements LexerInterface {

  /**
   * The regular expression for the operands.
   */
  const OPERAND_REGEXP = '([0-9]*)';

  /**
   * Valid character for operand.
   */
  const OPERAND_VALID_CHARS = '0-9';

  /**
   * An array of operators.
   *
   * @var array
   */
  protected $operators;

  /**
   * An array of tokens.
   *
   * @var array
   */
  private $tokens;

  /**
   * {@inheritdoc}
   */
  public function __construct(OperatorPluginManager $operatorManager) {
    $this->tokens = [];
    $this->operators = $operatorManager->loadAllOperators();
  }

  /**
   * {@inheritdoc}
   */
  public function getInfixTokens() {
    return $this->tokens;
  }

  /**
   * {@inheritdoc}
   */
  public function getTokens($expression) {

    if (!$this->isValidExpression($expression) || !$this->haveInvalidTokens($expression)) {
      throw new InvalidTokenException("The expression contains invalid tokens");
    }

    $regexp = sprintf('/%s/', $this->getAllRegex());
    preg_match_all($regexp, $expression, $matches);

    $this->tokens = [];
    foreach ($matches[0] as $key => $token) {
      // Used for validate the current char and so the infix expression.
      $previous_token = end($this->tokens);
      // Remove all spaces from the token.
      $token = trim($token);
      if ($token != '' && $this->haveInvalidTokens($token)) {
        if ($previous_token) {
          // Is not allowed having two operator next each other.
          if ($this->isOperator($token) && $this->isOperator($previous_token->value())) {
            throw new MalformedExpressionException("The expression have two followed operators without operand.");
          }
          // Is not allowed having two followed operands without an operator
          // in the middle.
          if ($this->isOperand($token) && $this->isOperand($previous_token->value())) {
            throw new MalformedExpressionException("The expression have two followed operands without any operator. $token");
          }
        }
        $this->tokens[] = new Token($token, $key);

      }
    }

    return $this->tokens;
  }

  /**
   * {@inheritdoc}
   */
  public function isValidExpression($expression) {
    $regexp = sprintf('/%s/', $this->getAllRegex());
    // Check if the expression contains only valid tokens.
    if (!preg_match($regexp, $expression, $matches)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperators() {
    return $this->operators;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperatorsRegex() {
    $operators = [];
    // Scan all the operators.
    foreach ($this->operators as $key => $value) {
      $operators[] = '\\' . $key;
    }
    $regexp = sprintf('([%s]?)', implode('|', $operators));

    return $regexp;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperandsRegex() {
    return self::OPERAND_REGEXP;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllRegex() {
    $regexp = [];

    // Add the only operand supported.
    $regexp[] = $this->getOperandsRegex();
    $regexp[] = $this->getOperatorsRegex();

    // Build the final expression.
    $regexp = implode('|', $regexp);

    return $regexp;
  }

  /**
   * {@inheritdoc}
   */
  public function isOperator($op) {
    $operator = sprintf('/^%s$/', $this->getOperatorsRegex());
    if (preg_match($operator, $op)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isOperand($op) {
    $operand = sprintf('/^%s$/', $this->getOperandsRegex());
    if (preg_match($operand, $op)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function haveInvalidTokens($op) {
    // Get all the valid characters for operands.
    $valid_chars[] = self::OPERAND_VALID_CHARS;
    // Get all the valid chars for operators.
    foreach ($this->operators as $key => $operator) {
      $valid_chars[] = sprintf('\%s', $key);
    }
    // Add empty characters as valid string (spaces, new lines, etc..).
    $valid_chars[] = '\s';

    $regexp = sprintf('/[^%s]/', implode('', $valid_chars));

    if (!preg_match($regexp, $op, $matches)) {
      return TRUE;
    }
    return FALSE;
  }

}
