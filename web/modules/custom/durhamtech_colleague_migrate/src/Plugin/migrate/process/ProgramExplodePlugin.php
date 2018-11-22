<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Explode Program type data.
 *
 * @MigrateProcessPlugin(
 *   id = "durhamtech_program_explode"
 * )
 */
class ProgramExplodePlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return FALSE;
    }
    $result = [];
    $new_value = explode(PHP_EOL, $value);
    foreach ($new_value as $key => $item) {
      preg_match('/\((.+)\)/', $item, $match);
      if (!empty($match[1])) {
        $result[$key] = $match[1];
      }
    }
    return $result;
  }

}
