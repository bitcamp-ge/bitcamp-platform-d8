<?php

namespace Drupal\bcmp_user;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Session\AccountProxy;

/**
 *
 */
class UserUtilServices {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * AccountProxy for current user.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   *
   */
  public function __construct(EntityTypeManager $entityTypeManager, AccountProxy $currentUser) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
  }

  /**
   *
   */
  public function setUserPassword($password) {
    try {
      /** @var \Drupal\user\Entity\User $user */
      $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
      $user->setPassword($password);
      $user->set('field_social_auth_password', true);
      $user->save();
    }
    catch (InvalidPluginDefinitionException $e) {
      $this->loggerFactory->get('girchi_users')->error($e->getMessage());
    }
  }

}
