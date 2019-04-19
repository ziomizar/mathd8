MATHEMATICAL LEXER AND PARSER in Drupal 8
-----------------------------------------
  
 * Introduction
 * Installation
 * Configuration
 * Maintainers
 
INTRODUCTION
------------

The mathd8 module implement a Lexer and Parser to evaluate simple mathematical expressions.
It can be used as a field formatter for the text field, to display the expression with 
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
 * enable the mathd8 using "drush en mathd8" or from the administration `/admin/modules`   
 
 * for further information about how to install a contributed Drupal module. Visit:
   https://www.drupal.org/documentation/install/modules-themes/modules-8

CONFIGURATION
-------------

 This module provide a field formatter that can be used in all the "text" fields. To configure it use the formatter "Mathematical parser" on a text field from the "Manage display".
 
 As an example to enable the "Mathematical parser" on the body field of the "Article" nodes:
 
 - go to `Administration > Structure > Content types > Article` 
 `[/admin/structure/types/manage/article]` 
 - Click on the tab `Manage display`
 - Select `Mathematical parser`
 - Enable or disable the `animation` checkbox in order to simulate the evaluation in the frontend.
 - "Save"
 

MAINTAINERS
-----------

Current maintainers:
 * ziomizar - https://www.drupal.org/u/ziomizar 
   



