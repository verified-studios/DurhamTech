<?php

namespace Drupal\durhamtech_colleague_migrate\Form;

use Drupal;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
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

    $form['courses_fieldset']['rollback_courses'] = array(
      '#type' => 'submit',
      '#value' => t('Rollback Course Taxonomy'),
      '#submit' => array('::rollbackCourses')
    );

    $form['courses_fieldset']['run_courses'] = array(
      '#type' => 'submit',
      '#value' => t('Run Course migrate'),
      '#submit' => array('::runCourses'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('course_data_taxonomy'),
    );

    $form['class_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Class',
    ];

    $form['class_fieldset']['clear_class'] = array(
      '#type' => 'submit',
      '#value' => t('Clear Classes'),
      '#submit' => array('::clearClass')
    );

    $form['class_fieldset']['run_class'] = array(
      '#type' => 'submit',
      '#value' => t('Run Class migrate'),
      '#submit' => array('::runClass'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('class_data_node'),
    );

    $form['programs_course_list_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Programs Courselist',
    ];

    $form['programs_course_list_fieldset']['rollback_programs_course_list'] = array(
      '#type' => 'submit',
      '#value' => t('Rollback Programs Course List Taxonomy'),
      '#submit' => array('::rollbackProgramsCourseList')
    );

    $form['programs_course_list_fieldset']['run_programs_course_list'] = array(
      '#type' => 'submit',
      '#value' => t('Run Programs Course List migrate'),
      '#submit' => array('::runProgramsCourseList'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('programs_course_list_taxonomy'),
    );


    $form['programs_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Programs',
    ];

    $form['programs_fieldset']['set_programs_inactive'] = array(
      '#type' => 'submit',
      '#value' => t('Set Programs Inactive'),
      '#submit' => array('::setProgramsInactive')
    );

    $form['programs_fieldset']['run_program'] = array(
      '#type' => 'submit',
      '#value' => t('Run Programs migrate'),
      '#submit' => array('::runPrograms'),
      '#suffix' => 'Last Updated Date: ' . $this->migrateLastImported('program_data_node'),
    );

    $form['data_refresh_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => 'Refresh Data',
    ];

    $form['data_refresh_fieldset']['data_refresh'] = array(
      '#type' => 'submit',
      '#value' => t('Refresh Data'),
      '#submit' => array('::dataRefresh'),
      '#prefix' => 'This will clear and refresh all of the data from the Colleague feed.<br />',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Clear Course Taxonomy
   */
  function dataRefresh() {
    $this->rollbackCourses();
    $this->runCourses();
    $this->clearClass();
    $this->runClass();
    $this->rollbackProgramsCourseList();
    $this->runProgramsCourseList();
    $this->setProgramsInactive();
    $this->runPrograms();
  }

  /**
   * Clear Course Taxonomy
   */
  function rollbackCourses() {
    $migration_id = 'course_data_taxonomy';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->rollback();
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
   * Clear Classes
   */
  function clearClass() {
    $result = \Drupal::entityQuery('node')
      ->condition('type', 'class')
      ->execute();
    entity_delete_multiple('node', $result);
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
   * Clear Programs Course List
   */
  function rollbackProgramsCourseList() {
    $migration_id = 'programs_course_list_taxonomy';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->rollback();
  }

  /**
   * Run Class migrate
   */
  function runProgramsCourseList() {
    $migration_id = 'programs_course_list_taxonomy';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

  /**
   * Set Programs Inactive
   */
  function setProgramsInactive() {
    \Drupal::database()->truncate('node__field_active')->execute();
    \Drupal::database()->truncate('node_revision__field_active')->execute();
  }

  /**
   * Run Programs migrate
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
