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

  public function __construct() {
    $this->view_argument_01 = 'magazines';
    $this->view_argument_02 = 'articles';
  }

  public function ajaxCallbackLoadRelated($nid_app, $nid_sub) {

    // Instantiates the response object
    $response = new AjaxResponse();

    // Tests if the argument received is numeric.
    if(!is_numeric($nid_app) OR !is_numeric($nid_sub)) {
      $response->addCommand(new AlertCommand('Valid numeric IDs must be provided for application and related content. That didn\'t happen, though.'));
      return $response;
    }

    // Loads the application node object.
    $node = \Drupal::entityManager()->getStorage('node')->load($nid_app);

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

    // Gets the associated Sub Segment.
    $child_node = \Drupal::entityManager()->getStorage('node')->load($nid_sub);

    // Halt execution of a parent subsegment cannot be found.
    if(!$child_node) {
      $response->addCommand(new AlertCommand("An associated content could not be loaded. The ID was {$nid_sub}"));
      return $response;
    }

    // Get the title for the parent Sub Segment.
    $child_node_title = $child_node->getTitle();

    $response->addCommand(new AlertCommand('The title is ' . $child_node_title));

    // Gets a list of associated content.
    $view_id = $this->view_argument_01;
    $view = Views::getView($view_id);
    $view->setDisplay($this->view_argument_02);
    $view->setArguments([$nid_app]);
    $views_output_array = $view->buildRenderable();

    // Prepares rendering
    $renderer = \Drupal::service('renderer');
    $views_output_rendered = $renderer->render($views_output_array);


    $title_text = t('Items for <strong>@title_parent</strong> > <strong>@title_child</strong>', 
      [ 
        '@title_parent' => $child_node_title,
        '@title_child' => $title,
      ]
    );

    // Adds a wrapper ID around the output.
    $output = "
  <div id=\"subsegment-products\" class=\"industrial-application-products\">
    <div class=\"subsegment-products-inner\">
      <h2>{$title_text}</h2>
      <a class=\"close-button use-ajax\" href=\"/industrial/applications/close\" title=\"" . t("Close the list of products.") . "\">x</a>
      {$views_output_rendered}
    </div>
  </div>";

    // Proceeds with the appropriate replacements.
    $response->addCommand( new ReplaceCommand( '#subsegment-products', $output ) );

    // Fades sibling elements.
    $response->addCommand( 
      new InvokeCommand(
        NULL,
        "disableSiblings",
        array(
          array(
            'target' => ".link-sub-id-{$nid_sub}",
            'comparison_layer' => '.item-list',
            'speed_transition' => 250,
            'partial_opacity' => 0.35
          )
        ) 
      )
    );

    // Fades current list sibling elements.
    $response->addCommand( 
      new InvokeCommand(
        NULL,
        "disableSiblings",
        array(
          array(
            'target' => ".link-app-id-{$nid_app}",
            'comparison_layer' => '.industry-subsegment-wrapper',
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
            'target' => "#subsegment-products",
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

    $response->addCommand(
      new InvokeCommand(
        '.industrial-application-products',
        'fadeOut'
      )
    );

    // Fades sibling layers of the clicked item.
    $response->addCommand(
      new InvokeCommand(
        NULL,
        'restoreDefaults',
        array(
          array(
            'target' => ".industry-subsegment-wrapper",
            'speed_transition' => 250
          )
        )
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
            'target' => "#block--views-block--bl-industrial-applications",
            'speed_transition' => 500,
          )
        )
      )
    );

/*
    // Fades in other layers (not the one corresponding to the Industrial application clicked)
    $commands[] = ajax_command_invoke(
      NULL,
      "restoreDefaults",
      array(
        array(
          'target' => ".node-industrial-application",
          'speed_transition' => 250
        )
      )
    );

    
    $commands[] = ajax_command_invoke(
      NULL,
      "scrollTo",
      array(
        array(
          'target' => "#node-full-content-subsegments-wrapper",
          'speed_transition' => 500,
        )
      )
    );

    $page = array(
      '#type' => 'ajax',
      '#commands' => $commands,
    );

    ajax_deliver($page);
*/


    return $response;

  }

}