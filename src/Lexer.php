<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;
use Drupal\mathd8\Plugin\OperatorPluginManager;
use Drupal\mathd8\Exception\InvalidTokenException;

/**
 * Class Lexer.
 */
class Lexer implements LexerInterface {

  /**
   * The regular expression for the operands.
   */
  const OPERAND_REGEXP = '([0-9]*)';

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
    $regexp = $this->getAllRegex();
    // TODO: make space insensitive the expression.
    preg_match_all($regexp, $expression, $matches);
    $this->tokens = [];
    foreach ($matches[0] as $key => $token) {
      // Remove all spaces from the token.
      $token = trim($token);
      switch (TRUE) {
        case empty($token):
          // Is a space or an empty character.
          continue;

        case $this->isValid($token):
          $this->tokens[] = new Token($token, $key);
          break;

        default:
          // Has been used an invalid non empty character.
          throw new InvalidTokenException(sprintf("Token %s is not a valid token.", $token));
      }
    }
    return $this->tokens;
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
    $regexp = sprintf('/%s/', implode('|', $regexp));

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
  public function isValid($op) {
    if (preg_match($this->getAllRegex(), $op)) {
      return TRUE;
    }
    return FALSE;
  }

}
