<?php

namespace Drupal\parkson_subsegments_products\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text which will work as a placeholder for loading 
 * Industrial Aplication products list.
 *
 * @Block(
 *   id = "block--industrial-application-products-list",
 *   admin_label = @Translation("Industrial Application Products List"),
 *   category = @Translation("Parkson Custom")
 * )
 */
class IndustrialApplicationProductsList extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'markup',
      '#markup' => "<div id=\"subsegment-products\" class=\"subsegment-products-wrapper\"></div>",
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['industrial_application_products_list_settings'] = $form_state->getValue('industrial_application_products_list_settings');
  }
}