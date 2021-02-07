<?php

namespace Drupal\bcmp_user\Controller;

use Drupal\bcmp_user\EmailSenderService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserDataInterface;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
   * Email sender service.
   *
   * @var EmailSenderService
   */
  protected $emailSenderService;

  /**
   * Email sender service.
   *
   * @var MessengerInterface
   */
  protected $messenger;


  /**
   * Constructs a BitCampUserPageController object.
   *
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   * @param \Drupal\user\UserDataInterface $user_data
   *   The user data service.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param EmailSenderService $emailSenderService
   * @param MessengerInterface $messenger
   */
  public function __construct(
		UserStorageInterface $user_storage,
		UserDataInterface $user_data,
		LoggerInterface $logger,
    EmailSenderService $emailSenderService,
    MessengerInterface $messenger){
	    $this->userStorage = $user_storage;
  	  $this->userData = $user_data;
    	$this->logger = $logger;
    	$this->emailSenderService = $emailSenderService;
    	$this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
      ->getStorage('user'),
      $container->get('user.data'),
      $container->get('logger.factory')
      ->get('user'),
      $container->get('bcmp_users.email_services'),
      $container->get('messenger'));
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

  /**
   * @param Request $request
   */
  public function sendEmail(Request $request){
    $result = $this->emailSenderService->sendVerificationEmail();
    return new JsonResponse($result);
  }

  /**
   * @param Request $request
   */
  public function verifyEmail(Request $request){
    $code = $request->query->get('code');
    if(!empty($code)) {
      /** @var User $currentUser */
      $currentUser = $this->userStorage->load($this->currentUser()->id());
      $randomHash = $currentUser->get('field_random_hash')->value;
      if($code === $randomHash) {
        if(!$currentUser->get('field_email_is_verified')->value) {
          $currentUser->set('field_email_is_verified', TRUE);
          $currentUser->save();
          $this->messenger->addStatus("თქვენ წარმატებით გაიარეთ ელ.ფოსტის ვერიფიკაცია");
        } else {
          $this->messenger->addWarning("თქვენი ელ.ფოსტა უკვე ვერიფიცირებულია");
        }
      } else {
        $this->messenger->addError("მოხდა შეცდომა, გთხოვთ თავიდან ცადოთ ელ.ფოსტის ვერიფიკაცია");
      }
    }

    return new RedirectResponse('/user');
  }
}

