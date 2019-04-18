CONTENTS OF THIS FILE
---------------------
   
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

 * For a full description of the module, visit the project page:
   githubpage
    
INSTALLATION
------------
 
 * clone the repository to the module folder
 
 git clone repoid

CONFIGURATION
-------------

 This module provide a field formatter that can be used in all the "text" fields.
 To configure it for example for the body of the "Article" nodes go to 
 * Administration > Structure > Content types > Article
 
 - Click on the tab "Manage display"
 - Select "Mathematical parser"
 - Enable or disable an animation on the parsing 
 - "Save"
 

MAINTAINERS
-----------

Current maintainers:
 * ziomizar - https://www.drupal.org/u/ziomizar 
   



