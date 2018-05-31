<?php

use Drupal\migrate\MigrateMessage;
use Drupal\migrate_tools\MigrateExecutable;

function migrateFinishedCallback($success, $results, $operations) {
  if ($success) {
    $message = t('Data successfully imported.');
    \Drupal::logger('durhamtech_colleague_migrate')->info('Data successfully imported.');
  }
  else {
    $message = t('Finished with an error.');
    \Drupal::logger('durhamtech_colleague_migrate')->info('Import finished with an error.');
  }
  drupal_set_message($message);
}

/**
* Run Programs migrate Batch.
*/
function runProgramsBatch($ids, $uri, &$context) {
  /** @var \Drupal\migrate\Plugin\Migration $migration */
  $plugin = Drupal::service('plugin.manager.migration');
  $migration = $plugin->createInstance('program_data_node', [
    'source' => [
      'path' => $uri,
    ],
  ]);
  $context['results'][] = [$ids];
  $message = new MigrateMessage();
  $message->display('Start Program importing data.');
  $migration->getIdMap()->prepareUpdate();
  $executable = new MigrateExecutable($migration, $message, ['idlist' => $ids]);
  $executable->import();
  $context['message'] = $message;
}

/**
 * Run Class migrate Batch.
 */
function runClassBatch($ids, $uri, &$context) {
  /** @var \Drupal\migrate\Plugin\Migration $migration */
  $plugin = Drupal::service('plugin.manager.migration');
  $migration = $plugin->createInstance('class_data_node', [
    'source' => [
      'path' => $uri,
    ],
  ]);
  $context['results'][] = [$ids];
  $message = new MigrateMessage();
  $message->display('Start Class importing data.');
  $migration->getIdMap()->prepareUpdate();
  $executable = new MigrateExecutable($migration, $message, ['idlist' => $ids]);
  $executable->import();
  $context['message'] = $message;
}

/**
 * Run Courses migrate Batch.
 */
function runCoursesBatch($ids, $uri, &$context) {
  /** @var \Drupal\migrate\Plugin\Migration $migration */
  $plugin = Drupal::service('plugin.manager.migration');
  $migration = $plugin->createInstance('course_data_taxonomy', [
    'source' => [
      'path' => $uri,
    ],
  ]);
  $context['results'][] = [$ids];
  $message = new MigrateMessage();
  $message->display('Start Courses importing data.');
  $migration->getIdMap()->prepareUpdate();
  $executable = new MigrateExecutable($migration, $message, ['idlist' => $ids]);
  $executable->import();
  $context['message'] = $message;
}
