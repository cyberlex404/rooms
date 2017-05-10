<?php

namespace Drupal\rooms\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Room entities.
 *
 * @ingroup rooms
 */
interface RoomInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Room name.
   *
   * @return string
   *   Name of the Room.
   */
  public function getName();

  /**
   * Sets the Room name.
   *
   * @param string $name
   *   The Room name.
   *
   * @return \Drupal\rooms\Entity\RoomInterface
   *   The called Room entity.
   */
  public function setName($name);

  /**
   * Gets the Room creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Room.
   */
  public function getCreatedTime();

  /**
   * Sets the Room creation timestamp.
   *
   * @param int $timestamp
   *   The Room creation timestamp.
   *
   * @return \Drupal\rooms\Entity\RoomInterface
   *   The called Room entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Room published status indicator.
   *
   * Unpublished Room are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Room is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Room.
   *
   * @param bool $published
   *   TRUE to set this Room to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\rooms\Entity\RoomInterface
   *   The called Room entity.
   */
  public function setPublished($published);

}
