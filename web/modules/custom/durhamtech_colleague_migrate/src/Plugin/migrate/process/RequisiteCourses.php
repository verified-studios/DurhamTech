<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Prepare course_outline data.
 *
 * @MigrateProcessPlugin(
 *   id = "requisite_courses"
 * )
 */
class RequisiteCourses extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $requisite_courses_data_items = [];
    $taxonomyStorage = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');
    $courses = $taxonomyStorage->loadTree('course');

    if (!empty($courses)) {
      $requisite_courses_array = explode(',', $value);
      foreach ($courses as $course) {
        if (in_array($course->name, $requisite_courses_array)) {
          $requisite_courses_data_items[$course->tid][] = $course->tid;
        }
      }
    }
    return $requisite_courses_data_items;
  }

}
