<?php

namespace Drupal\durhamtech_colleague_migrate\Form;

use Drupal;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\file\Entity\File;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\MigrateExecutable;

/**
 * @file
 * Contains \Drupal\durhamtech_colleague_migrate\Form\DtechMigrationSettingsForm.
 */

/**
 * Implements an example form.
 */
class DtechMigrationManageForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'durhamtech_colleague_migrate_manage';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['courses_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Courses',
    ];

    $form['courses_fieldset']['run_courses'] = array(
      '#type' => 'submit',
      '#value' => t('Run Courses migrate'),
      '#submit' => array('::runCourses'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('course_data_taxonomy'),
    );

    $form['class_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Class',
    ];

    $form['class_fieldset']['run_class'] = array(
      '#type' => 'submit',
      '#value' => t('Run Class migrate'),
      '#submit' => array('::runClass'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('class_data_node'),
    );

    $form['programs_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Programs',
    ];

    $form['programs_fieldset']['run_program'] = array(
      '#type' => 'submit',
      '#value' => t('Run Programs migrate'),
      '#submit' => array('::runPrograms'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('program_data_node'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Run Courses migrate.
   */
  function runCourses() {
    $migration_id = 'course_data_taxonomy';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

  /**
   * Run Class migrate.
   */
  function runClass() {
    $migration_id = 'class_data_node';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

  /**
   * Run Programs migrate.
   */
  function runPrograms() {
    $migration_id = 'program_data_node';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

  /**
   * Gets migrate last imported.
   */
  function migrateLastImported($migrate_id) {
    $migrate_last_imported_store = \Drupal::keyValue('migrate_last_imported');
    $last_imported = $migrate_last_imported_store->get($migrate_id, FALSE);
    if ($last_imported) {
      /** @var DateFormatter $date_formatter */
      $date_formatter = \Drupal::service('date.formatter');
      $result = $date_formatter->format($last_imported / 1000,
        'custom', 'Y-m-d H:i:s');
    }
    else {
      $result = 'Never started';
    }
    return $result;
  }

  /**
   * Parse CSV.
   */
  function parseCSV($uri, $id = NULL) {
    $data = [];
    $rows = array_map('str_getcsv', file($uri));
    array_shift($rows);
    foreach ($rows as $key => $row) {
      if (!empty($id)) {
        $needed_id = array_slice($row, 1, 1);
        $data[$key] = array_shift($needed_id);
      }
      else {
        $data[$key] = array_shift($row);
      }
    }

    return $data;

  }

}
