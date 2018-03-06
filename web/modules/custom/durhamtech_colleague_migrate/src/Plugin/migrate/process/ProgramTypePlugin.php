<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;

/**
 * Saving Program type fields.
 *
 * @MigrateProcessPlugin(
 *   id = "durhamtech_program_type",
 *   handle_multiples = TRUE
 * )
 */
class ProgramTypePlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $return = $tids = [];
    if (empty($value)) {
      return FALSE;
    }
    foreach ($value as $term_name) {
      $term = taxonomy_term_load_multiple_by_name($term_name, 'program_type');
      $tids[][0] = key($term);
    }
    $value = $tids;
    if (is_array($value) || $value instanceof \Traversable) {
      foreach ($value as $key => $new_value) {
        $new_row = new Row($new_value, []);
        $migrate_executable->processRow($new_row, $this->configuration['process']);
        $destination = $new_row->getDestination();
        if (array_key_exists('key', $this->configuration)) {
          $key = $this->transformKey($key, $migrate_executable, $new_row);
        }
        $return[$key] = $destination;
      }
    }
    return $return;
  }

  /**
   * Runs the process pipeline for the key to determine its dynamic name.
   *
   * @param string|int $key
   *   The current key.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migrate executable helper class.
   * @param \Drupal\migrate\Row $row
   *   The current row after processing.
   *
   * @return mixed
   *   The transformed key.
   */
  protected function transformKey($key, MigrateExecutableInterface $migrate_executable, Row $row) {
    $process = ['key' => $this->configuration['key']];
    $migrate_executable->processRow($row, $process, $key);
    return $row->getDestinationProperty('key');
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return TRUE;
  }

}
