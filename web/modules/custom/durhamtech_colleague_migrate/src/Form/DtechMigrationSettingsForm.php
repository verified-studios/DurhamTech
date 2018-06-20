<?php

namespace Drupal\durhamtech_colleague_migrate\Form;

use Drupal;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
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
class DtechMigrationSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'durhamtech_colleague_migrate_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'durhamtech_colleague_migrate.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('durhamtech_colleague_migrate.settings');

    $form['courses_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Courses',
    ];

    $form['courses_fieldset']['dtech_courses_data'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Courses data file'),
      '#upload_location' => 'public://import/course',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#default_value' => $config->get('dtech_courses_data'),
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

    $form['class_fieldset']['dtech_class_data'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Class data file'),
      '#upload_location' => 'public://import/class',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#default_value' => $config->get('dtech_class_data'),
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

    $form['programs_fieldset']['dtech_programs_data'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Programs data file'),
      '#upload_location' => 'public://import/program',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#default_value' => $config->get('dtech_programs_data'),
    ];

    $form['programs_fieldset']['run_program'] = array(
      '#type' => 'submit',
      '#value' => t('Run Programs migrate'),
      '#submit' => array('::runPrograms'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('program_data_node'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->configFactory->getEditable('durhamtech_colleague_migrate.settings')
      ->set('dtech_courses_data', $form_state->getValue('dtech_courses_data'))
      ->set('dtech_class_data', $form_state->getValue('dtech_class_data'))
      ->set('dtech_programs_data', $form_state->getValue('dtech_programs_data'))
      ->save();

    $fid_courses_data = $form_state->getValue(['dtech_courses_data', 0]);
    if (!empty($fid_courses_data)) {
      $file = File::load($fid_courses_data);
      $file->setPermanent();
      $file->save();
      \Drupal::logger('durhamtech_colleague_migrate')->info('A new file was uploaded for the migration of the Courses. File name: @file', ['@file' => $file->getFilename()]);
    }

    $fid_class_data = $form_state->getValue(['dtech_class_data', 0]);
    if (!empty($fid_class_data)) {
      $file = File::load($fid_class_data);
      $file->setPermanent();
      $file->save();
      \Drupal::logger('durhamtech_colleague_migrate')->info('A new file was uploaded for the migration of the Classes. File name: @file', ['@file' => $file->getFilename()]);
    }

    $fid_programs_data = $form_state->getValue(['dtech_programs_data', 0]);
    if (!empty($fid_programs_data)) {
      $file = File::load($fid_programs_data);
      $file->setPermanent();
      $file->save();
      \Drupal::logger('durhamtech_colleague_migrate')->info('A new file was uploaded for the migration of the Programs. File name: @file', ['@file' => $file->getFilename()]);
    }
  }

  /**
   * Run Courses migrate.
   */
  function runCourses() {
    $operations = [];
    $config = $this->config('durhamtech_colleague_migrate.settings');
    $fid = $config->get('dtech_courses_data');
    if (empty($fid)) {
      return FALSE;
    }
    $file = File::load($fid[0]);
    $uri = $file->getFileUri();
    $data = $this->parseCSV($uri, 'Courses');
    foreach ($data as $key => $row) {
      $operations[$key] = [
        'runCoursesBatch',
        [count($data) === 1 ? $row : implode(',', $row), $uri],
      ];
    }
    $batch = array(
      'title' => t('Migrating...'),
      'operations' => $operations,
      'finished' => 'migrateFinishedCallback',
      'file' => drupal_get_path('module', 'durhamtech_colleague_migrate') . '/durhamtech_colleague_migrate.batch.inc',
    );
    batch_set($batch);
  }

  /**
   * Run Class migrate.
   */
  function runClass() {
    $operations = [];
    $config = $this->config('durhamtech_colleague_migrate.settings');
    $fid = $config->get('dtech_class_data');
    if (empty($fid)) {
      return FALSE;
    }
    $file = File::load($fid[0]);
    $uri = $file->getFileUri();
    $data = $this->parseCSV($uri);
    foreach ($data as $key => $row) {
      $operations[$key] = [
        'runClassBatch',
        [count($data) === 1 ? $row : implode(',', $row), $uri],
      ];
    }
    $batch = array(
      'title' => t('Migrating...'),
      'operations' => $operations,
      'finished' => 'migrateFinishedCallback',
      'file' => drupal_get_path('module', 'durhamtech_colleague_migrate') . '/durhamtech_colleague_migrate.batch.inc',
    );
    batch_set($batch);
  }

  /**
   * Run Programs migrate.
   */
  function runPrograms() {
    $operations = [];
    $config = \Drupal::config('durhamtech_colleague_migrate.settings');
    $fid = $config->get('dtech_programs_data');
    if (empty($fid)) {
      return FALSE;
    }
    $file = File::load($fid[0]);
    $uri = $file->getFileUri();
    $data = $this->parseCSV($uri);
    foreach ($data as $key => $row) {
      $operations[$key] = [
        'runProgramsBatch',
        [count($data) === 1 ? $row : implode(',', $row), $uri],
      ];
    }
    $batch = array(
      'title' => t('Migrating...'),
      'operations' => $operations,
      'finished' => 'migrateFinishedCallback',
      'file' => drupal_get_path('module', 'durhamtech_colleague_migrate') . '/durhamtech_colleague_migrate.batch.inc',
    );
    batch_set($batch);
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
