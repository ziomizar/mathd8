<?php

namespace Drupal\mathd8\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

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
class FilterMathParser extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {

    /** @var \Drupal\mathd8\ParserInterface $parser */
    $parser = \Drupal::service('mathd8.parser');

    $text_cleaned = $filter = new FilterProcessResult(_filter_html_escape($text));
    $result = $parser->getEvaluationSteps($text_cleaned);

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

    $filter = new FilterProcessResult($output);
    $filter->setAttachments(array(
      'library' => array('mathd8/mathd8-js'),
    ));

    return $filter;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Only spaces, integer numbers and + - / * operators are allowed. Its possible write and compute just one expression.');
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

}
