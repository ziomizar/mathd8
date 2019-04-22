MATHEMATICAL LEXER AND PARSER in Drupal 8
-----------------------------------------
  
 * Introduction
 * Installation
 * Configuration
 * Maintainers
 
INTRODUCTION
------------

The mathd8 module implement a Lexer and Parser to evaluate simple mathematical expressions.
It can be used as a field formatter for the text field, or as an input format to display the expression with 
the result. 
It can show an animation that visualize the steps and the grouping applied by the algorithm.

For a full description of the module, visit the project page:
https://github.com/ziomizar/mathd8

LIMITATIONS
-----------

The current version, it only support:
 - basic operators: + - / *
 - integer numbers
    
INSTALLATION
------------
 
 * clone the repository to the module folder 
   `git clone git@github.com:ziomizar/mathd8.git`
 * enable the mathd8 using "drush en mathd8" or from the administration (/admin/modules)   
 
 For further information about how to install a contributed Drupal module. Visit:
  https://www.drupal.org/documentation/install/modules-themes/modules-8

CONFIGURATION
-------------

 This module provide a field formatter that can be used in all the "text" fields and a text filter. 

 FIELD FORMATTER
 
 This field formatter is available just for `Text (plain, long)` and `Text (plain)`.  
 Select the formatter "Mathematical parser" on a text field from the "Manage display" of a node type.
 As an example to enable the "Mathematical parser" on a new field `math` in the node "Article":
 
 - Go to `Administration > Structure > Content types > Article` (/admin/structure/types/manage/article) 
 - Create a new field of type `Text (plain, long)` or `Text (plain)` and call it `math` 
 - Click on the tab `Manage display`
 - Select `Mathematical parser` in the column `FORMAT` of the `math` field
 - Click on the engine icon to open the setting to enable or disable the `animation` checkbox in order to simulate the evaluation in the frontend.
 - "Save"
 
 TEXT FILTER
 
 The module provide a text filter that can be used as input format, 
 to enable the mathematical parser on an existing text format:
 - Go to the Text Formats and Editors page (admin/config/content/formats)
     and configure the desired input formats to enable the filter.
 - Enable `Display any text as a mathematical expression`     
 - Rearrange the Filter processing order to resolve conflicts with other filters.
      
 If you want to create a new text format:
 - `+ Add text format` on (admin/config/content/formats) 
 - Give a name to the new text format
 - Select the Roles allowed to use the new text format
 - Enable `Display any text as a mathematical expression`
 - Save
 
 Mathematical parser text filter have an option to enable or disable the animation on the configuration form.

 Then the filter will be available in the text fields with the `text format` option list, 
 as for example usually is the body.
 
 The expression have to be written as [formula:1+2+3+4*5*6] and can be used several expressions per field.
 

MAINTAINERS
-----------

Current maintainers:
 * ziomizar - https://www.drupal.org/u/ziomizar 
   



