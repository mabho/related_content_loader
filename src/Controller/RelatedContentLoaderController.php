<?php

namespace Drupal\related_content_loader\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use \Drupal\views\Views;
use Drupal\Core\Controller\ControllerBase;
//use Drupal\Core\EntityManager;

/**
 * Provides a callback which will load a list of items associated with a given content.
 */
class RelatedContentLoaderController extends ControllerBase {

  /**
   * The ID of the view being queried.
   */
  protected $view_argument_01;

  /**
   * The ID of the block or attachment being queried.
   */
  protected $view_argument_02;

  /**
   * The ID of the layer being targeted and replaced with the View content being loaded by this class.
   */
  protected $layer_id;

  /**
   * The ID of an element being used as target destination for scrolling.
   */
  protected $scroll_back_to_id;

  public function __construct() {
    $this->view_argument_01 = 'magazines';
    $this->view_argument_02 = 'bl_articles';
    $this->layer_id = 'target-layer';
    $this->scroll_back_to = 'base-layer';
  }

  public function ajaxCallbackLoadRelated($nid) {

    // Instantiates the response object
    $response = new AjaxResponse();

    // Tests if the argument received is numeric.
    if(!is_numeric($nid)) {
      $response->addCommand(new AlertCommand('Valid numeric IDs must be provided for application and related content. That didn\'t happen, though.'));
      return $response;
    }

    // Loads the application node object.
    $node = \Drupal::entityManager()->getStorage('node')->load($nid);

    // If a valid node could not be found, deliver empty result.
    if(!$node) {
      $response->addCommand(new AlertCommand('A valid node could not be loaded.'));
      return $response;
    }

    // Get the title we need for the current Industrial application.
    $title = $node->getTitle();

    // Tests if a valid reference is set
    /*
    $sub_segment_item = $node->get('field_ref_sub_segment')->first();

    if( !isset($sub_segment_item) ) {
      $response->addCommand(new AlertCommand('This Sub Segment hasn\'t been set yet.'));
      return $response;
    }

    // Now, get the parent subsegment object.
    $sub_segment_node = $sub_segment_item
      ->get('entity')
      ->getTarget()
      ->getValue()
    ;
    */

    // Gets a list of associated content.
    $view_id = $this->view_argument_01;
    $view = Views::getView($view_id);
    $view->setDisplay($this->view_argument_02);
    $view->setArguments([$nid]);
    $views_output_array = $view->buildRenderable();

    // Prepares rendering
    $renderer = \Drupal::service('renderer');
    $views_output_rendered = $renderer->render($views_output_array);


    $title_text = t('Items associated with <strong>@title</strong>', 
      [ 
        '@title' => $title,
      ]
    );

    // Adds a wrapper ID around the output.
    $layer_id = $this->layer_id;
    $output = "
  <div id=\"{$layer_id}\">
    <div class=\"{$layer_id}-inner\">
      <h2>{$title_text}</h2>
      <a class=\"close-button use-ajax\" href=\"/related-content-loader/close\" title=\"" . t("close") . "\">x</a>
      {$views_output_rendered}
    </div>
  </div>";

  //$response->addCommand(new AlertCommand('The layer id is ' . $layer_id));

    // Proceeds with the appropriate replacements.
    $response->addCommand( new ReplaceCommand( "#{$layer_id}", $output ) );

    // Fades sibling elements.
    $response->addCommand( 
      new InvokeCommand(
        NULL,
        "disableSiblings",
        array(
          array(
            'target' => ".link-id-{$nid}",
            'comparison_layer' => '.item-list',
            'speed_transition' => 250,
            'partial_opacity' => 0.35
          )
        ) 
      )
    );

    // Scrolls to desired position.
    $response->addCommand( 
      new InvokeCommand(
        NULL,
        "scrollTo",
        array(
          array(
            'target' => "#{$layer_id}",
            'speed_transition' => 500
          )
        ) 
      )
    );

    return $response;

  }

/**
 * Provides a callback function which will close the detailed list of products.
 */
  public function ajaxCallbackClose() {

    // Instantiates the response object
    $response = new AjaxResponse();

    $layer_id = $this->layer_id;
    $scroll_back_to_id = $this->scroll_back_to_id;

    $response->addCommand(
      new InvokeCommand(
        "#{$layer_id}",
        'fadeOut'
      )
    );

    // Fades in other layers (not the one corresponding to the Subsegment clicked)
    $response->addCommand(
      new InvokeCommand(
        NULL,
        'restoreDefaults',
        array(
          array(
            'target' => ".item-list",
            'speed_transition' => 250
          )
        )
      )
    );

    // Scrolls to the top of the Subsegments block
    $response->addCommand(
      new InvokeCommand(
        NULL,
        'scrollTo',
        array(
          array(
            'target' => "#{$scroll_back_to_id}",
            'speed_transition' => 500,
          )
        )
      )
    );

    return $response;

  }

}