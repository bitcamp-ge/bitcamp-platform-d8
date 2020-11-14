<?php

namespace Drupal\bcmp_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserDataInterface;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Defines BitCampUserPageController class.
 */
class BitCampUserPageController extends ControllerBase {
  
  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The user data service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;


  /**
   * Constructs a BitCampUserPageController object.
   *
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   * @param \Drupal\user\UserDataInterface $user_data
   *   The user data service.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(
		UserStorageInterface $user_storage, 
		UserDataInterface $user_data, 
		LoggerInterface $logger) {
	    $this->userStorage = $user_storage;
  	  $this->userData = $user_data;
    	$this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container
      ->get('entity.manager')
      ->getStorage('user'), $container
      ->get('user.data'), $container
      ->get('logger.factory')
      ->get('user'));
  }


	/**
	 * Redirects user to Phase 1 profile page.
	 *
   * This controller assumes that it is only invoked for authenticated users.
   * This is enforced for the 'user.page' route with the '_user_is_logged_in'
   * requirement. 
	 * 
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Returns a redirect to the profile of the currently logged in user.
   */
	public function phase1Page(){
		 return $this
    	->redirect('profile.user_page.single',
				 [
					'user' => $this->currentUser()->id(),
					'profile_type' => 'phase_1' 
         ]
			);
	}
}

