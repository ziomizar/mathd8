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
   * @var Lexer
   */
  protected $lexer;

  /**
   * The parser service.
   *
   * @var Parser
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
   * @dataProvider expressionLexerProvider
   *
   * @param string $expression
   *   The mathematical extpression.
   *
   * @param array $expected
   *   The expected result.
   */
  public function testLexerTokenizer($expression, array $expected) {
    $result = $this->lexer->getTokens($expression);
    $this->assertEquals($expected, $result);
  }

  /**
   * Test if the Lexer convert to postfix properly the expression.
   *
   * @dataProvider postfixLexerProvider
   *
   * @param $expression
   *   The mathematical extpression.
   *
   * @param $expected
   *   The expected result.
   */
  public function testLexerToPosix($expression, $expected) {
    $result = $this->parser->toPostfix($expression);
    $this->assertEquals($expected, $result);
  }

  /**
   * Test if the Parser compute properly the expression.
   *
   * @dataProvider expressionParserProvider
   *
   * @param $expression
   *   The mathematical expression.
   *
   * @param $expected
   *   The expected result.
   */
  public function testParserResult($expression, $expected) {
    $result = $this->parser->evaluate($expression);
    $this->assertEquals($expected, $result);
  }

  /**
   * The parser data provider.
   *
   * @return array
   */
  public function expressionParserProvider() {
    return [
      ['1 * 2 / 3 - 4', -3.3333333333333],
      ['1 + 2 * 8 / 9', 2.77777777778],
      ['4 / 2 * 100 - 2 + 80', 278],
    ];
  }

  /**
   * The lexer data provider.
   *
   * @return array
   */
  public function expressionLexerProvider() {
    return [
      ['1 * 2 / 3 - 4', [1, '*', 2, '/', 3, '-', 4]],
      ['1 + 2 * 8 / 9', [1, '+', 2, '*', 8, '/', 9]],
      ['4 / 2 * 100 - 2 + 80', [4, '/', 2, '*', 100, '-', 2, '+', 80]],
    ];
  }

  /**
   * The lexer data provider.
   *
   * @return array
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
