<?php

namespace Drupal\durhamtech_colleague_migrate\Form;

use Drupal;
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
    }

    $fid_class_data = $form_state->getValue(['dtech_class_data', 0]);
    if (!empty($fid_class_data)) {
      $file = File::load($fid_class_data);
      $file->setPermanent();
      $file->save();
    }

    $fid_programs_data = $form_state->getValue(['dtech_programs_data', 0]);
    if (!empty($fid_programs_data)) {
      $file = File::load($fid_programs_data);
      $file->setPermanent();
      $file->save();
    }
  }

  /**
   * Run Courses migrate.
   */
  function runCourses() {
    $config = $this->config('durhamtech_colleague_migrate.settings');
    $fid = $config->get('dtech_courses_data');
    if (empty($fid)) {
      return FALSE;
    }
    $file = File::load($fid[0]);
    $uri = $file->getFileUri();

    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $plugin = Drupal::service('plugin.manager.migration');
    $migration = $plugin->createInstance('course_data_taxonomy', [
      'source' => [
        'path' => $uri,
      ],
    ]);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();

  }

  /**
   * Run Class migrate.
   */
  function runClass() {
    $config = $this->config('durhamtech_colleague_migrate.settings');
    $fid = $config->get('dtech_class_data');
    if (empty($fid)) {
      return FALSE;
    }
    $file = File::load($fid[0]);
    $uri = $file->getFileUri();

    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $plugin = Drupal::service('plugin.manager.migration');
    $migration = $plugin->createInstance('class_data_node', [
      'source' => [
        'path' => $uri,
      ],
    ]);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

  /**
   * Run Programs migrate.
   */
  function runPrograms() {
    $config = $this->config('durhamtech_colleague_migrate.settings');
    $fid = $config->get('dtech_programs_data');
    if (empty($fid)) {
      return FALSE;
    }
    $file = File::load($fid[0]);
    $uri = $file->getFileUri();

    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $plugin = Drupal::service('plugin.manager.migration');
    $migration = $plugin->createInstance('program_data_node', [
      'source' => [
        'path' => $uri,
      ],
    ]);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

}
