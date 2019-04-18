(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.Mathd8Behavior = {
    attach: function (context, settings) {
      
      function executeStep(step) {
        // Get the operands and operation of this step.
        $('.token-' + step.op1).addClass('token-active token');
        $('.token-' + step.operator).addClass('token-active token');
        $('.token-' + step.op2).addClass('token-active token');
        
        // Group together all the token active in this step and group
        // them with parenthesis.
        $('.token-active')
          .wrapAll("<div class='token-result " + 'token-' + step.result + "' />");
        
        // All the token of this step has been evaluated set them to default.
        $('.token-active').removeClass('token-active');
        
      }
      
      function parse() {
        var steps = drupalSettings.mathd8.steps;
        if (drupalSettings.mathd8.animation) {
          $('.mathd8-expression').once('Mathd8Behavior').each(function () {
            $.each(steps, function (index, step) {
              setTimeout(function () {
                executeStep(step);
              }, 2500 * index);
            })
          });
        }
      }
      
      parse();
    }
  };
})(jQuery, Drupal, drupalSettings);