<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Saving room fields.
 *
 * @MigrateProcessPlugin(
 *   id = "durhamtech_room_migrate"
 * )
 */
class ClassParagraphPlugin extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $class_data_items = [];
    $source = $row->getSource();

    // Prepare rooms data.
    $room_value = $source['Room'];
    if (!is_string($room_value)) {
      throw new MigrateException(sprintf('%s is not a string', var_export($room_value, TRUE)));
    }
    $rooms_row = explode(PHP_EOL, $room_value);
    foreach ($rooms_row as $room_key => $room) {
      $class_data_items[$room_key]['field_room'] = $room;
    }

    // Prepare Meeting days data.
    $days_value = $source['Meeting Days'];
    $day_rows = explode(PHP_EOL, $days_value);
    foreach ($day_rows as $row_key => $day_row) {
      if (strpos($day_row, 'ü')) {
        $class_data_items[$row_key]['field_meeting_days'] = explode('ü', $day_row);
      }
      else {
        $class_data_items[$row_key]['field_meeting_days'] = $day_row;
      }
    }

    // Prepare Start Time.
    $start_time_value = $source['Start Time'];
    $start_time_row = explode(PHP_EOL, $start_time_value);
    foreach ($start_time_row as $start_time_key => $start_time) {
      $class_data_items[$start_time_key]['field_start_time'] = $start_time;
    }

    // Prepare End Time.
    $end_time_value = $source['End Time'];
    $end_time_row = explode(PHP_EOL, $end_time_value);
    foreach ($end_time_row as $end_time_key => $end_time) {
      $class_data_items[$end_time_key]['field_end_time'] = $end_time;
    }

    // Get node Id.
    $nid = $row->getIdMap()['destid1'];
    if (empty($nid)) {
      return;
    }
    /** @var Node $node */
    $node = Node::load($nid);
    if (empty($node)) {
      return;
    }

    $node_paragraphs = $node->field_class_item;
    $first_paragraph_entity = $node_paragraphs[0]->entity;

    if (!empty($first_paragraph_entity)) {
      foreach ($node_paragraphs as $node_paragraph_key => $node_paragraph) {
        /** @var Paragraph $node_paragraph_entity */
        $node_paragraph_entity = $node_paragraph->entity;
        foreach ($class_data_items[$node_paragraph_key] as $field_name => $field_value) {
          $node_paragraph_entity->set($field_name, $field_value);
        }
        $node_paragraph_entity->save();
      }
    }
    else {
      foreach ($class_data_items as $item) {
        $paragraph = Paragraph::create(['type' => 'class']);
        foreach ($item as $field_name => $field_value) {
          $paragraph->set($field_name, $field_value);
        }
        $paragraph->save();
        $node->field_class_item->appendItem($paragraph);
      }
    }

    $node->save();

  }

}