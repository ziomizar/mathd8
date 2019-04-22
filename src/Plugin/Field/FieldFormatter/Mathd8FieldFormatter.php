<?php

namespace Drupal\mathd8\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mathd8\Parser;

/**
 * Plugin implementation of the 'mathd_parser_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "mathd8_parser",
 *   label = @Translation("Mathematical parser"),
 *   field_types = {
 *     "string",
 *     "string_long"
 *   }
 * )
 */
class Mathd8FieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The mathematical parser service.
   *
   * @var \Drupal\mathd8\Parser
   */
  protected $parser;

  /**
   * Constructs a FormatterBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\mathd8\Parser $parser
   *   The mathd8 parser service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, Parser $parser) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('mathd8.parser')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Enable or disable the animation.
      'animation' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['animation'] = [
      '#title' => $this->t('Animation'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('animation'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $value = $this->viewValue($item);
      $elements[$delta] = [
        '#animation' => (bool) $this->getSetting('animation'),
        '#result' => $value['result'],
        '#raw' => $value['expression'],
        '#tokens' => $value['tokens'],
        '#steps' => $value['steps'],
        '#cache' => [
          'max-age' => 0,
        ],
        '#theme' => 'mathd8_field',
        '#attached' => [
          'library' => [
            'mathd8/mathd8-js',
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * The function return all the data needed to build the expression and its
   * result. It have also extra information needed to run an animation.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return array
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $output = [];
    $output['raw'] = Html::escape($item->value);
    $output = $this->parser->getEvaluationSteps($output['raw']);
    return $output;
  }

}
