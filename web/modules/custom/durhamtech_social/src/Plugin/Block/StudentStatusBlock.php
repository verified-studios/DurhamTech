<?php

namespace Drupal\durhamtech_social\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "student_status_block",
 *   admin_label = @Translation("Student Status Block"),
 * )
 */
class StudentStatusBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('Tabs');
    $output = '<div class="vertical-tabs">';
    $output_menu = '<ul class="nav nav-pills nav-stacked col-md-3">';
    $output_body = '<div class="tab-content col-md-8">';
    foreach ($terms as $key => $term) {
      if ($term->tid != 2434) {
        if ($key !== 1) {
          $output_menu .= '<li><a data-toggle="tab" href="#' . $term->tid . '">' . \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid)->get('field_tab_text')->getValue()[0]['value'] . '</a></li>';
          $output_body .= '<div class="tab-pane" id="' . $term->tid . '">';
        }
        else {
          $output_menu .= '<li class="active"><a data-toggle="tab" href="#' . $term->tid . '">' . \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid)->get('field_tab_text')->getValue()[0]['value'] . '</a></li>';
          $output_body .= '<div class="tab-pane active" id="' . $term->tid . '">';
        }
        $output_body .= "<h2>{$term->name}</h2>";
        $output_body .= \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid)->get('field_tab_body')->getValue()[0]['value'];
        foreach (\Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid)->get('field_tab_links') as $key => $item) {
          if (!empty($item->getValue()['title'])) {
            $output_body .= '<a href="' . $item->getUrl()->toString() . '" class = "btn">' . $item->getValue()['title'] . '</a> &nbsp; ';
          }
        }
        $output_body .= '</div>';
      }
    }
    $output_menu .= '</ul>';

    $output .= $output_menu . $output_body . '</div>';

    return [
      '#markup' => $output
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
