<?php

namespace Drupal\hermes_hmail_webform_connexion;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Hmail Config entities.
 */
class HmailConfigListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Hmail config');
    $header['altered_webform'] = $this->t('Altered Webform');
//    $header['id'] = $this->t('Machine name');
    $header['hmail_base_url'] = $this->t('Hmail base url');
    $header['application_origin'] = $this->t('Application origin');
    $header['csv_mapper_id'] = $this->t('CSV mapper');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\hermes_hmail_webform_connexion\Entity\HmailConfig */
    $row['label'] = $entity->label();
    $row['altered_webform'] = $entity->get('altered_webform');
//    $row['id'] = $entity->id();
    $row['hmail_base_url'] = $entity->get('hmail_base_url');
    $row['application_origin'] = $entity->get('application_origin');
    $row['csv_mapper_id'] = $entity->get('csv_mapper_id');

    return $row + parent::buildRow($entity);
  }

}
