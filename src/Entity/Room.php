<?php

namespace Drupal\rooms\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Room entity.
 *
 * @ingroup rooms
 *
 * @ContentEntityType(
 *   id = "room",
 *   label = @Translation("Room"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rooms\RoomListBuilder",
 *     "views_data" = "Drupal\rooms\Entity\RoomViewsData",
 *     "translation" = "Drupal\rooms\RoomTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\rooms\Form\RoomForm",
 *       "add" = "Drupal\rooms\Form\RoomForm",
 *       "edit" = "Drupal\rooms\Form\RoomForm",
 *       "delete" = "Drupal\rooms\Form\RoomDeleteForm",
 *     },
 *     "access" = "Drupal\rooms\RoomAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\rooms\RoomHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "room",
 *   data_table = "room_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer room entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/rooms/{room}",
 *     "add-form" = "/admin/structure/room/add",
 *     "edit-form" = "/admin/structure/room/{room}/edit",
 *     "delete-form" = "/admin/structure/room/{room}/delete",
 *     "collection" = "/admin/structure/room",
 *   },
 *   field_ui_base_route = "room.settings"
 * )
 */
class Room extends ContentEntityBase implements RoomInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Room entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Room entity.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'max_length' => 250,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      //->setDescription(t('A Description.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'text_textfield',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['photos'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Photos'))
      ->setDescription(t('Room photos'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'file')
      ->setSetting('file_directory', 'rooms')
      ->setSetting('alt_field', FALSE)
      ->setSetting('alt_field_required', FALSE)
      ->setSetting('title_field', TRUE)
      ->setSetting('min_resolution', '600x600') // TODO goto settings
      ->setSetting('max_filesize', '10 Mb')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'image',
      ))
      ->setDisplayOptions('form', array(
        'type' => 'image',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['beds'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Number of beds'))
      ->setDescription(t('Number of beds.'))
      ->setSetting('min', 1)
      ->setSetting('max', 50)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'weight' => 4,
      ))
      ->setDisplayConfigurable('form', true)
      ->setDisplayConfigurable('view', true)
      ->setDefaultValue(0);

    // maximum number of people
    $fields['people'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Maximum of people'))
      ->setDescription(t('Maximum number of people.'))
      ->setSetting('min', 1)
      ->setSetting('max', 300)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'weight' => 4,
      ))
      ->setDisplayConfigurable('form', true)
      ->setDisplayConfigurable('view', true)
      ->setDefaultValue(0);


    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Room is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
