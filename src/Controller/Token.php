<?php

namespace Drupal\mathd8\Controller;

/**
 * Class Token.
 *
 * @package Drupal\mathd8\Controller
 */
class Token {

  /**
   * The position of the token in the expression.
   *
   * @var int
   */
  protected $position;

  /**
   * The value of the token.
   *
   * @var mixed
   */
  protected $value;

  /**
   * Token constructor.
   *
   * @param mixed $value
   *   The token value.
   * @param int $position
   *   The position of the token.
   */
  public function __construct($value, $position) {
    $this->position = $position;
    $this->value = $value;
  }

  /**
   * Get the position of the token.
   *
   * @return int
   *   The token position.
   */
  public function position() {
    return $this->position;
  }

  /**
   * The value of the token.
   *
   * @return mixed
   *   The token value.
   */
  public function value() {
    return $this->value;
  }

}
