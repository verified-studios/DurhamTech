<?php

namespace Drupal\durhamtech_colleague_migrate\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DurhamtechColleagueMigrateSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public function onMigratePostImport(MigrateImportEvent $event) {
    // $fetcher_plugin = 'migration_plugin';
    // $row = $event->getRow();
    $migration = $event->getMigration();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MigrateEvents::POST_IMPORT => 'onMigratePostImport',
    ];
  }
}
