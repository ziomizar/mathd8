<?php

namespace Drupal\Tests\mathd8\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\mathd8\Exception\InvalidTokenException;
use Drupal\mathd8\Exception\MalformedExpressionException;

/**
 * Tests Lexer and Parser.
 *
 * @group mathd8
 */
class Mathd8Test extends KernelTestBase {

  /**
   * The Lexer service.
   *
   * @var \Drupal\mathd8\Lexer
   */
  protected $lexer;

  /**
   * The parser service.
   *
   * @var \Drupal\mathd8\Parser
   */
  protected $parser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['mathd8'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->lexer = $this->container->get('mathd8.lexer');
    $this->parser = $manager = $this->container->get('mathd8.parser');
  }

  /**
   * Test if the Lexer tokenize properly the expression.
   *
   * @param string $expression
   *   The mathematical extpression.
   * @param array $expected
   *   The expected result.
   *
   * @throws \Drupal\mathd8\Exception\InvalidTokenException.
   *   In case has been used an invalid token in the expression.
   * @throws \Drupal\mathd8\Exception\MalformedExpressionException.
   *   In case has been used an invalid token in the expression.
   *
   * @dataProvider expressionLexerProvider
   */
  public function testLexerTokenizer($expression, array $expected) {
    /** @var \Drupal\mathd8\Controller\Token[] $result */
    $result = $this->lexer->getTokens($expression);
    array_walk($result, function (&$token) {
      $token = $token->value();
    });
    $this->assertEquals($expected, $result);
  }

  /**
   * Test if the Lexer convert to postfix properly the expression.
   *
   * @param string $expression
   *   The mathematical extpression.
   * @param array $expected
   *   The expected result.
   *
   * @dataProvider postfixLexerProvider
   */
  public function testLexerToPosix($expression, array $expected) {
    /** @var \Drupal\mathd8\Controller\Token[] $result */
    $result = $this->parser->toPostfix($expression);
    array_walk($result, function (&$token) {
      $token = $token->value();
    });
    $this->assertEquals($expected, $result);
  }

  /**
   * Test if the Parser compute properly the expression.
   *
   * @param string $expression
   *   The mathematical expression.
   * @param float $expected
   *   The expected result.
   *
   * @throws \Drupal\mathd8\Exception\InvalidTokenException.
   *   In case has been used an invalid token in the expression.
   * @throws \Drupal\mathd8\Exception\MalformedExpressionException.
   *   In case has been used an invalid token in the expression.
   *
   * @dataProvider expressionParserProvider
   */
  public function testParserResult($expression, $expected) {
    /** @var \Drupal\mathd8\Controller\Token $result */
    $result = $this->parser->evaluate($expression);
    $this->assertEquals($expected, ($result ? $result->value() : ''));
  }

  /**
   * Test if the Parser manage properly the invalidTokenException.
   *
   * @param string $expression
   *   The mathematical expression.
   *
   * @throws \Drupal\mathd8\Exception\InvalidTokenException.
   *   In case has been used an invalid token in the expression.
   * @throws \Drupal\mathd8\Exception\MalformedExpressionException.
   *   In case has been used an invalid token in the expression.
   *
   * @dataProvider invalidTokenProvider
   */
  public function testInvalidTokenParserResult($expression) {
    $this->setExpectedException(InvalidTokenException::class);
    /** @var \Drupal\mathd8\Controller\Token $result */
    $this->parser->evaluate($expression);
  }

  /**
   * Test if the Parser manage properly the malformedExpressionException.
   *
   * @param string $expression
   *   The mathematical expression.
   *
   * @throws \Drupal\mathd8\Exception\InvalidTokenException.
   *   In case has been used an invalid token in the expression.
   * @throws \Drupal\mathd8\Exception\MalformedExpressionException.
   *   In case has been used an invalid token in the expression.
   *
   * @dataProvider malformedExpressionProvider
   */
  public function testMalformedExpressionParserResult($expression) {
    $this->setExpectedException(MalformedExpressionException::class);
    /** @var \Drupal\mathd8\Controller\Token $result */
    $this->parser->evaluate($expression);
  }

  /**
   * The parser data provider.
   *
   * @return array
   *   Array of expressions with their results.
   */
  public function expressionParserProvider() {
    return [
      ['', ''],
      [' ', ''],
      ['80', '80'],
      ['1 * 2 / 3 - 4', -3.3333333333333],
      ['1 + 2 * 8 / 9', 2.77777777778],
      ['4 / 2 * 100 - 2 + 80', 278],
    ];
  }

  /**
   * The lexer (infix) data provider.
   *
   * @return array
   *   Array of expression and tokenization.
   */
  public function expressionLexerProvider() {
    return [
      ['', []],
      [' ', []],
      ['80', ['80']],
      ['1 * 2 / 3 - 4', [1, '*', 2, '/', 3, '-', 4]],
      ['1 + 2 * 8 / 9', [1, '+', 2, '*', 8, '/', 9]],
      ['4 / 2 * 100 - 2 + 80', [4, '/', 2, '*', 100, '-', 2, '+', 80]],
    ];
  }

  /**
   * The lexer (postfix) data provider.
   *
   * @return array
   *   Array of expression and tokenization.
   */
  public function postfixLexerProvider() {
    return [
      ['1 * 2', [1, 2, '*']],
      ['1 + 2', [1, 2, '+']],
      ['1 / 2', [1, 2, '/']],
      ['1 - 2', [1, 2, '-']],
      ['4 / 2 * 100 - 2 + 80', [4, 2, '/', 100, '*', 2, '-', 80, '+']],
      ['4 / 2 * 100 - 2 + 80 / 2 * 1111',
        [4, 2, '/', 100, '*', 2, '-', 80, 2, '/', 1111, '*', '+'],
      ],
    ];
  }

  /**
   * Invalid tokens data provider.
   *
   * @return array
   *   Array of expressions with invalid tokens.
   */
  public function invalidTokenProvider() {
    return [
      ['1 + 2 * 8 / 9LLLL'],
      ['4 /Z 2 * 100 - 2 + 80'],
      ['@ 100 - 2 + 80'],
      ['10.0 - 2 + 80'],
    ];
  }

  /**
   * Malformed expressions data provider.
   *
   * @return array
   *   Array of malformed expressions.
   */
  public function malformedExpressionProvider() {
    return [
      // Operator at the beginning, end or both.
      ['1 * 2 / 3 - 4 +'],
      ['+ 1 * 2 / 3 - 4'],
      ['+ 1 * 2 / 3 - 4 -'],
      // Double operator without operands.
      ['1 + 2 * + 8 / 9'],
      ['4 / / 2 * / 100 - 2 + 80'],
      // Double operands without operators.
      ['4 2 * / 100 - 2 + 80'],
      ['4 + 2 8 8 * / 100 100 - 2 + 80'],
      // Single number or empty expression.
      ['80 80'],
    ];
  }

}
