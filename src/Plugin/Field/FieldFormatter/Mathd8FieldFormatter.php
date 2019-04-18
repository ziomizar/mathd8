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
use Drupal\mathd8\Mathd8Parser;

/**
 * Plugin implementation of the 'mathd_parser_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "mathd8_parser",
 *   label = @Translation("Mathematical parser"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
class Mathd8FieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The mathematical parser service.
   *
   * @var \Drupal\mathd8\Mathd8Parser
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
   * @param \Drupal\mathd8\Mathd8Parser $mathd8_parser
   *   The mathd8 parser service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, Mathd8Parser $mathd8_parser) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->parser = $mathd8_parser;
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
        '#result' => $value['result'],
        '#raw' => $value['expression'],
        '#tokens' => $value['tokens'],
        '#cache' => [
          'max-age' => 0,
        ],
        '#theme' => 'mathd8_field',
        '#attached' => [
          'library' => [
            'mathd8/mathd8-js',
          ],
          'drupalSettings' => [
            'mathd8' => [
              'steps' => $value['steps'],
              'animation' => $this->getSetting('animation'),
            ],
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return array
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    $output = [];
    $output['raw'] = Html::escape($item->value);
    if ($this->parser->evaluate($output['raw'])) {
      $output['result'] = $this->parser->evaluate($output['raw'])->value();
      $output['tokens'] = $this->parser->expression();
      $output['tokens'] = $this->toArray($output['tokens']);
      $output['steps'] = $this->parser->steps();
    }
    return $output;
  }

  /**
   * Convert the array of tokens into an associative array.
   *
   * @params Drupal\mathd8\Controller\Token[]
   *   The array of tokens.
   *
   * @return array
   *   The array of token as associative array.
   */
  public function toArray(array $tokens) {
    $output = [];
    foreach ($tokens as $token) {
      $output[] = [
        'value' => $token->value(),
        'position' => $token->position(),
      ];
    }
    return $output;
  }

}
