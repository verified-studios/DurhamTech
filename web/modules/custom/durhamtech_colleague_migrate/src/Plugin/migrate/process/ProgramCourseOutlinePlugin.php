<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Prepare course_outline data.
 *
 * @MigrateProcessPlugin(
 *   id = "durhamtech_course_outline"
 * )
 */
class ProgramCourseOutlinePlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $course_outline_data_items = [];
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree('course');

    if (!empty($terms)) {
      foreach ($terms as $term) {
        if (strripos($value, $term->name)) {
          $course_outline_data_items[$term->tid][] = $term->tid;
        }
      }
    }
    return $course_outline_data_items;
  }

}
