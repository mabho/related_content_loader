Related Content Loader is a Drupal 8 module which will help administrators set a Views to dynamically load items inside a container. The items loaded are associated to a given parent via entity reference.

Please, find explanation beneath on how to properly setup this module and related content to trigger the parent -> shild relationship. 

Use case
========
* Setup content type 1 - let's say, the 'magazine' content type.
* Setup content type 2 - let's say, the 'article' content type. Articles are contained in magazines
* Setup a field associated with the 'article' content type to relate it to the parent 'magazine' bundle. Articles are contained in magazines.
* Add content of 'magazine' type.
* Add content of 'article' type and associate it with one of the parent 'magazine' items already created.


