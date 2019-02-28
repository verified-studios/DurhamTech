<?php
/**
 * @file Contains the code to generate the custom drush commands.
 */

use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;

/**
 * Implements hook_drush_command().
 */
function durhamtech_colleague_migrate_drush_command() {
  $commands['migrate-execute'] = [
    'description' => 'This will import all of the colleague data',
    'aliases' => ['me'],
  ];
  $commands['clear-extra-courses'] = [
    'description' => 'This will remove courses with the course id added.',
    'aliases' => ['cec'],
  ];
  return $commands;
}

/**
 * Drush command logic.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_durhamtech_colleague_migrate_migrate_execute() {
  $logger = \Drupal::logger('durhamtech_colleague_migrate');
  $logger->info('Started Course Migrate');
  _runMigration('course_data_taxonomy');
  $logger->info('Finished Course Migrate');
  $logger->info('Started Class Rollback');
  _runRollback('class_data_node');
  $logger->info('Finished Class Rollback');
  $logger->info('Started Class Migrate');
  _runMigration('class_data_node');
  $logger->info('Finished Class Migrate');
  $logger->info('Started Adding Class Data to Courses');
  classCourse();
  $logger->info('Finished Adding Class Data to Courses');
  $logger->info('Started Programs Course List Migrate');
  _runMigration('programs_course_list_taxonomy');
  $logger->info('Finished Programs Course List  Migrate');
  $logger->info('Started Program Migrate');
  _runMigration('program_data_node');
  $logger->info('Finished Program Migrate');
}

/**
 * Drush command logic.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_durhamtech_colleague_migrate_clear_extra_courses() {
  $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  $courses = $term_storage->loadTree('course');
  foreach ($courses as $tid => $course){
    $name = $course->name;
    $x = strpos($name, '-', strpos($name, '-')+1);
    echo $course->tid . ' | ' . $course->name .' | ' . $x . ' | ' . is_int($x) .  "\n";
    if (is_int($x)){
      $term_storage->load($course->tid)->delete();
    }
  }

}

/**
 * Run Migration.
 */
function _runMigration($migration_id) {
  /** @var \Drupal\migrate\Plugin\Migration $migration */
  $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
  $migration->getIdMap()->prepareUpdate();
  $executable = new MigrateExecutable($migration, new MigrateMessage());
  $executable->import();
}

/**
 * Run Rollback
 */
function _runRollback($migration_id) {
  $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
  $migration->getIdMap()->prepareUpdate();
  $executable = new MigrateExecutable($migration, new MigrateMessage());
  $executable->rollback();
}

