<?php

namespace Drupal\mathd8;

use Drupal\mathd8\Controller\Token;
use Drupal\mathd8\Plugin\OperatorPluginManager;

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
    $regexp = $this->getRegExp();
    // TODO: make space insensitive the expression.
    preg_match_all($regexp, $expression, $matches);
    $this->tokens = [];
    foreach ($matches[0] as $key => $token) {
      $token = trim($token);
      if (empty($token)) {
        continue;
      }
      else {
        if (preg_match($this->getRegExp(), $token)) {
          $this->tokens[] = new Token($token, $key);
        }
        else {
          // TODO: return exception.
        }
      }
    }
    return $this->tokens;
  }

  /**
   * Build the regexp used to define all the tokens in the expression.
   */
  protected function getRegExp() {
    $regexp = [];

    // Add the only operand supported.
    $regexp[] = self::OPERAND_REGEXP;

    $operators = [];
    // Scan all the operators.
    foreach ($this->operators as $key => $value) {
      $operators[] = '\\' . $key;
    }
    $regexp[] = '([' . implode('|', $operators) . ']?)';

    // Build the final expression.
    $regexp = '/' . implode('|', $regexp) . '/';

    return $regexp;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperators() {
    return $this->operators;
  }

}

