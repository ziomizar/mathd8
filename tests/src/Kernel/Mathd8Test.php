<?php

namespace Drupal\Tests\mathd8\Kernel;

use Drupal\KernelTests\KernelTestBase;

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
   *
   * @dataProvider expressionParserProvider
   */
  public function testParserResult($expression, $expected) {
    /** @var \Drupal\mathd8\Controller\Token $result */
    $result = $this->parser->evaluate($expression);
    $this->assertEquals($expected, $result->value());
  }

  /**
   * The parser data provider.
   *
   * @return array
   *   Array of expressions with their results.
   */
  public function expressionParserProvider() {
    return [
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
      ['4 / 2 × 100 - 2 + 80', [4, 2, 100, '/', 2, '-', 80, '+']],
      ['4 / 2 × 100 - 2 + 80 / 2 * 1111',
        [4, 2, 100, '/', 2, '-', 80, 2, '/', 1111, '*', '+'],
      ],
    ];
  }

}
