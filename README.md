Related Content Loader
======================

Related Content Loader is a Drupal 8 module which will help administrators set a Views to dynamically load items inside a container. The items loaded are associated to a given parent via entity reference.

Please, find explanation beneath on how to properly setup this module and related content to trigger the parent -> shild relationship. 

Use case
--------
* Setup content type 1 - let's say, the 'magazine' content type.
* Setup content type 2 - let's say, the 'article' content type. Articles are contained in magazines
* Setup a field associated with the 'article' content type to relate it to the parent 'magazine' bundle. Articles are contained in magazines.
* Add content of 'magazine' type.
* Add content of 'article' type and associate it with one of the parent 'magazine' items already created.
* Finally, create a view with the following characteristics:
  * View 1 - can be of type 'page':
    * Select content of entity 'node'.
    * Filters:
      * Filter by content type and pick the parent bundle type.
    * For the 'Title' link, check the option 'Output this field as a custom link' and apply the following settings: 
      * Change the link destination to '/related-content-loader/{{ nid }}'
      * Add the custom class 'use-ajax'. This is very important because it will make the link behave differently and trigger an AJAX call.
  * View 2 - can be of type block.
    * Select content of entity 'node'.
    * Filters:
      * Filter by content type and pick the child bundle type.
    * Arguments:
      * Add an argument that corresponds to the field that relates child to its parent content type.
      * When setting up the argument, under 'When the filter is not available', select 'Provide default value' and the option 'Content ID from URL'.

Customization
-------------
This module is still very simple in terms of how it is built, it can be improved in a number of ways. By editing lines 39 ~ 42 of file /src/Controller/RelatedContentLoaderController.php you can change the default parameters and adapt the script to your specific needs by changing its default behavior:
  * $view_argument_01 - then name of the view being queried to pull all items related with the parent element.
  * $view_argument_02 - then name of the block or attachment.
  * layer_id - the ID of the empty layer where the content of the view will be loaded.
  * scroll_back_to - the ID of an element where the page will scroll to once the 'close' button is clicked.

Demo
----
[There is a working version of this script here](http://drupal-224396-1393232.cloudwaysapps.com/magazines). What is happening?
* A list of two published nodes of type 'magazine'.
* Click any of them to see the nodes if type 'article' associated with each one.