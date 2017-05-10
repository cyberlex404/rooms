<?php

namespace Drupal\rooms\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Room entities.
 */
class RoomViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
