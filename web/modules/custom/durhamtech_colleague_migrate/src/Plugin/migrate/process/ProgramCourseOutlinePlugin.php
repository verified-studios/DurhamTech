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
    //$value_array = explode(',', $value);
    $taxonomyStorage = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');
    $courses = $taxonomyStorage->loadTree('course');

    if (!empty($courses)) {
      $programs_course_taxonomy_entry = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', 'programs_course_list')
        ->condition('field_program_id', $value)
        ->execute();
      if (!empty($programs_course_taxonomy_entry)) {
        $programs_course_taxonomy_entry_term_id = key($programs_course_taxonomy_entry);
        $program_courses = $taxonomyStorage->load($programs_course_taxonomy_entry_term_id);
        if (!empty($program_courses)) {
          $required_courses = $program_courses->get('field_required_courses')->value;
          $required_courses_array = explode(',', $required_courses);
          foreach ($courses as $course) {
            if (in_array($course->name, $required_courses_array)) {
              $course_outline_data_items[$course->tid][] = $course->tid;
            }
          }
        }
      }
    }
    return $course_outline_data_items;
  }

}
