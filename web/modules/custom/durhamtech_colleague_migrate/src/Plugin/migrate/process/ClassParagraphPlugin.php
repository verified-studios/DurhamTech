<?php

namespace Drupal\durhamtech_colleague_migrate\Plugin\migrate\process;

use Drupal\Component\Datetime\DateTimePlus;
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
    $taxonomyStorage = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term');
    //$buildings = $taxonomyStorage->loadTree('building');

    $class_data_items = [];
    $source = $row->getSource();

    // Prepare Meeting days data.
    $days_value = $source['Meeting Days'];
    $day_rows = explode(', ', $days_value);
    $day_rows = array_filter($day_rows, function ($value) {
      return $value !== '';
    });
    foreach ($day_rows as $row_key => $day_row) {
      if (strpos($day_row, ',')) {
        $class_data_items[$row_key]['field_meeting_days'] = explode(',', $day_row);
      }
      else {
        $class_data_items[$row_key]['field_meeting_days'] = $day_row;
      }
    }



    // Prepare building data.
    $building_value = $source['Building'];
    if (!is_string($building_value)) {
      throw new MigrateException(sprintf('%s is not a string', var_export($building_value, TRUE)));
    }
    $building_row = explode(', ', $building_value);
    foreach ($building_row as $building_key => $building_code) {
      $building=$building_code;
      $buildings_taxonomy_entry = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', 'building')
        ->condition('field_building_code', $building_code)
        ->execute();
      if (!empty($buildings_taxonomy_entry)) {
        $buildings_taxonomy_entry_term_id = key($buildings_taxonomy_entry);
        $building_term = $taxonomyStorage->load($buildings_taxonomy_entry_term_id);
        if (!empty($building_term)) {
          $building = $building_term->name->value;
        }
      }
      $class_data_items[$building_key]['field_building'] = $building;
    }
    if (!empty($class_data_items[0]['field_building']) && $building_key < $row_key) {
      for ($i = $building_key + 1; $i <= $row_key; $i++) {
        $class_data_items[$i]['field_building'] = $building;
      }
    }
    // Prepare rooms data.
    $room_value = $source['Room'];
    if (!is_string($room_value)) {
      throw new MigrateException(sprintf('%s is not a string', var_export($room_value, TRUE)));
    }
    $rooms_row = explode(', ', $room_value);
    foreach ($rooms_row as $room_key => $room) {
      $class_data_items[$room_key]['field_room'] = $room;
    }

    if (!empty($class_data_items[0]['field_room']) && $room_key < $row_key) {
      for ($i = $room_key + 1; $i <= $row_key; $i++) {
        $class_data_items[$i]['field_room'] = $room;
      }
    }

    if (!empty($class_data_items[0]['field_meeting_days']) && $row_key < $room_key) {
      for ($i = $row_key + 1; $i <= $room_key; $i++) {
        $class_data_items[$i]['field_meeting_days'] = $class_data_items[0]['field_meeting_days'];
      }
    }

    // Prepare Start Date.
    $start_date_value = $source['Start Date'];
    $start_date_row = explode(', ', $start_date_value);
    $start_date_row = array_filter($start_date_row, function ($value) {
      return $value !== '';
    });
    $rowCount = sizeof($start_date_row);
    foreach ($start_date_row as $start_date_key => $start_date) {
      if ($this->validateDate($start_date)) {
        $class_data_items[$start_date_key]['field_start_date'] = $this->transformDate($start_date,'n/j/y','Y-m-d');
      }
    }

    if (!empty($class_data_items[0]['field_start_date']) && $start_date_key < $row_key) {
      for ($i = $start_date_key + 1; $i <= $row_key; $i++) {
        if ($this->validateDate($start_date)) {
          $class_data_items[$i]['field_start_date'] = $this->transformDate($start_date,'n/j/y','Y-m-d');
        }
      }
    }

    if (!empty($class_data_items[0]['field_start_date']) && $start_date_key < $room_key) {
      for ($i = $start_date_key + 1; $i <= $room_key; $i++) {
        if ($this->validateDate($start_date)) {
          $class_data_items[$i]['field_start_date'] = $this->transformDate($start_date, 'n/j/y', 'Y-m-d');
        }
      }
    }

    // Prepare End Date.
    $end_date_value = $source['End Date'];
    $end_date_row = explode(', ', $end_date_value);
    $end_date_row = array_filter($end_date_row, function ($value) {
      return $value !== '';
    });
    $rowCount = sizeof($end_date_row);
    foreach ($end_date_row as $end_date_key => $end_date) {
      if ($this->validateDate($end_date)) {
        $class_data_items[$end_date_key]['field_end_date'] = $this->transformDate($end_date,'n/j/y','Y-m-d');
      }
    }

    if (!empty($class_data_items[0]['field_end_date']) && $end_date_key < $row_key) {
      for ($i = $end_date_key + 1; $i <= $row_key; $i++) {
        if ($this->validateDate($end_date)) {
          $class_data_items[$i]['field_end_date'] = $this->transformDate($end_date, 'n/j/y', 'Y-m-d');
        }
      }
    }

    if (!empty($class_data_items[0]['field_end_date']) && $end_date_key < $room_key) {
      for ($i = $end_date_key + 1; $i <= $room_key; $i++) {
        if ($this->validateDate($end_date)) {
          $class_data_items[$i]['field_end_date'] = $this->transformDate($end_date, 'n/j/y', 'Y-m-d');
        }
      }
    }

    // Prepare Start Time.
    $start_time_value = $source['Start Time'];
    $start_time_row = explode(', ', $start_time_value);
    $start_time_row = array_filter($start_time_row, function ($value) {
      return $value !== '';
    });
    foreach ($start_time_row as $start_time_key => $start_time) {
      $class_data_items[$start_time_key]['field_start_time'] = $start_time;
    }

    if (!empty($class_data_items[0]['field_start_time']) && $start_time_key < $row_key) {
      for ($i = $start_time_key + 1; $i <= $row_key; $i++) {
        $class_data_items[$i]['field_start_time'] = $start_time;
      }
    }

    if (!empty($class_data_items[0]['field_start_time']) && $start_time_key < $room_key) {
      for ($i = $start_time_key + 1; $i <= $room_key; $i++) {
        $class_data_items[$i]['field_start_time'] = $start_time;
      }
    }

    // Prepare End Time.
    $end_time_value = $source['End Time'];
    $end_time_row = explode(', ', $end_time_value);
    $end_time_row = array_filter($end_time_row, function ($value) {
      return $value !== '';
    });
    foreach ($end_time_row as $end_time_key => $end_time) {
      $class_data_items[$end_time_key]['field_end_time'] = $end_time;
    }

    if (!empty($class_data_items[0]['field_end_time']) && $end_time_key < $row_key) {
      for ($i = $end_time_key + 1; $i <= $row_key; $i++) {
        $class_data_items[$i]['field_end_time'] = $end_time;
      }
    }

    if (!empty($class_data_items[0]['field_end_time']) && $end_time_key < $room_key) {
      for ($i = $end_time_key + 1; $i <= $room_key; $i++) {
        $class_data_items[$i]['field_end_time'] = $end_time;
      }
    }


    // Get node Id.
    $nid = isset($row->getIdMap()['destid1']) ? $row->getIdMap()['destid1'] : NULL;
    if (!empty($nid)) {
      /** @var Node $node */
      $node = Node::load($nid);
      if (!empty($node)) {
        $node_paragraphs = $node->field_class_item;
        $paragraphEntities = $node_paragraphs->referencedEntities();
        foreach ($paragraphEntities as $paragraphEntity) {
          $paragraphEntity->delete();
        }
      }
    }

    $classes = [];

    foreach ($class_data_items as $item) {
      $paragraph = Paragraph::create(['type' => 'class']);
      foreach ($item as $field_name => $field_value) {
        $paragraph->set($field_name, $field_value);
      }
      $paragraph->save();
      $paragraphRevisionId = $paragraph->getRevisionId();
      $classes[] = [
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraphRevisionId,
        ];
    }
    return $classes;
  }

  public function transformDate($value, $fromFormat, $toFormat) {
    if (empty($value) && $value !== '0' && $value !== 0) {
      return '';
    }

    if (isset($this->configuration['timezone'])) {
      @trigger_error('Configuration key "timezone" is deprecated in 8.4.x and will be removed before Drupal 9.0.0, use "from_timezone" and "to_timezone" instead. See https://www.drupal.org/node/2885746', E_USER_DEPRECATED);
      $from_timezone = $this->configuration['timezone'];
      $to_timezone = isset($this->configuration['to_timezone']) ? $this->configuration['to_timezone'] : NULL;
    }
    else {
      $system_timezone = date_default_timezone_get();
      $default_timezone = !empty($system_timezone) ? $system_timezone : 'UTC';
      $from_timezone = isset($this->configuration['from_timezone']) ? $this->configuration['from_timezone'] : $default_timezone;
      $to_timezone = isset($this->configuration['to_timezone']) ? $this->configuration['to_timezone'] : $default_timezone;
    }
    $settings = isset($this->configuration['settings']) ? $this->configuration['settings'] : [];

    // Attempts to transform the supplied date using the defined input format.
    // DateTimePlus::createFromFormat can throw exceptions, so we need to
    // explicitly check for problems.
    $transformed = DateTimePlus::createFromFormat($fromFormat, $value, $from_timezone, $settings)->format($toFormat, ['timezone' => $to_timezone]);

    return $transformed;
  }

  function validateDate($date, $delimiter = '/')
  {
    $tempDate = explode($delimiter, $date);

    if (sizeof($tempDate) < 3) {
      return false;
    }
    // checkdate(month, day, year)
    return checkdate($tempDate[0], $tempDate[1], $tempDate[2]);
  }

}
