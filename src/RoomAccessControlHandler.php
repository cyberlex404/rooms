<?php

namespace Drupal\rooms;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Room entity.
 *
 * @see \Drupal\rooms\Entity\Room.
 */
class RoomAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rooms\Entity\RoomInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished room entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published room entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit room entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete room entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add room entities');
  }

}
