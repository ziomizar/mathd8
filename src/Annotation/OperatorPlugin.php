<?php

namespace Drupal\mathd8\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Operator plugin item annotation object.
 *
 * @see \Drupal\mathd8\Plugin\OperatorPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class OperatorPlugin extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The symbol of the operator.
   *
   * @var string
   */
  public $symbol;

  /**
   * The precedence of the operator.
   *
   * @var int
   */
  public $precedence;

  /**
   * The orientation of the operator.
   *
   * @var string
   */
  public $orientation;

}
