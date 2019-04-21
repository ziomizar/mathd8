(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.Mathd8Behavior = {
    attach: function (context, settings) {
      
      function executeStep(element, expression) {
        var step = {};
        step.op1 = element.data("op1");
        step.op2 = element.data("op2");
        step.operator = element.data("operator");
        step.result = element.data("result-id");
        
        // Get the operands and operation of this step.
        expression.find('.token-' + step.op1).addClass('token-active token');
        expression.find('.token-' + step.operator).addClass('token-active token');
        expression.find('.token-' + step.op2).addClass('token-active token');
        
        // Group together all the token active in this step and group
        // them with parenthesis.
        expression.find('.token-active')
          .wrapAll("<div class='token-result " + 'token-' + step.result + "' />")
          .removeClass('token-active');
      }
      
      function parse() {
        if (drupalSettings.mathd8.animation) {
          $('.mathd8-expression.not-animated-yet').each(function () {
            let expressionObj = {};
            expressionObj.$obj = $(this);
            $(this).find('.steps .step').each(function (index, step) {
              let stepObj = {};
              stepObj.$obj = $(this);
              setTimeout(function () {
                executeStep(stepObj.$obj, expressionObj.$obj);
              }, 2500 * index);
            })
            // Compute this expression just one time removing the status not-animated-yet.
            expressionObj.$obj.removeClass('not-animated-yet');
          });
        }
      }
      
      parse();
    }
  };
})(jQuery, Drupal, drupalSettings);