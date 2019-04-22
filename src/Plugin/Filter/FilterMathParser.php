<?php

namespace Drupal\mathd8\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\mathd8\ParserInterface;

/**
 * Provides a filter to display any HTML as plain text.
 *
 * @Filter(
 *   id = "filter_mathd8_parser",
 *   title = @Translation("Display any text as a mathematical expression"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_HTML_RESTRICTOR,
 *   weight = -10
 * )
 */
class FilterMathParser extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The parser service.
   *
   * @var \Drupal\mathd8\ParserInterface
   */
  protected $parser;

  /**
   * Constructs the math filter plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\mathd8\ParserInterface $parser
   *   The token service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ParserInterface $parser) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('mathd8.parser')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $text_cleaned = $filter = new FilterProcessResult(_filter_html_escape($text));
    $text = $this->convertAllTags($text_cleaned);
    $filter = new FilterProcessResult($text);
    $filter->setAttachments(array(
      'library' => array('mathd8/mathd8-js'),
    ));

    return $filter;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('You can write expressions in the format [formula:1+2+3+4+5*12-14] only spaces, integer numbers and + - / * operators are allowed. Its possible write and compute multiple expressions in the same field, and write text around it.');
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['animation'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Animate the expression.'),
      '#default_value' => $this->settings['animation'],
      '#description' => $this->t('Animate the parsing of the expression.'),
    );
    return $form;
  }

  /**
   * Get a text convert all the [formula:?] tags into their values.
   *
   * @param string $text
   *   The field content where is possible found several mathematical tags.
   *
   * @return string
   *   The string with all token replaced.
   */
  protected function convertAllTags($text) {
    if (stristr(strtoupper($text), '[FORMULA') !== FALSE) {
      $text = preg_replace_callback('/\[FORMULA:(.*?)\]/i',
        [$this, 'evaluateTag'], $text);
    }
    return $text;
  }

  /**
   * Given an expression return its steps.
   *
   * @param string $match
   *   The expression inside a tag.
   *
   * @return string
   *   The html content of the tag.
   */
  protected function evaluateTag($match) {
    $result = $this->parser->getEvaluationSteps($match[1]);

    $animate_expression = $this->settings['animation'] ? 'not-animated-yet' : '';

    $output = sprintf('<div class="mathd8-expression %s">', $animate_expression);
    foreach ($result['expression'] as $token) {
      if ($token) {
        $output .= sprintf('<span class="token token-%s">%s</span>', $token->position(), $token->value());
      }
    }
    $output .= sprintf(' = %s', $result['result']);

    $output .= '<div class="steps">';
    foreach ($result['steps'] as $index => $step) {
      $output .= sprintf('<span class="step step-%s" data-op1="%s" data-op2="%s" data-operator="%s" data-result="%s" data-result-id="%s"></span>',
        $index, $step['op1'], $step['op2'], $step['operator'], $step['result_value'], $step['result']);
    }
    $output .= '</div>';

    $output .= '</div>';

    return $output;
  }

}
