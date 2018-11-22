<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * HTML and PHP tags strip from a given string.
 *
 * @MigrateProcessPlugin(
 *   id = "durhamtech_course_strip_tags"
 * )
 */
class CourseStripTags extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_string($value)) {
      throw new MigrateException('The input value must be a string.');
    }

    // Use optional start or length to return a portion of $value.
    $new_value = strip_tags($value);
    return $new_value;
  }

}
