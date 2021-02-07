<?php

namespace Drupal\bcmp_user;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Email sender service.
 */
class EmailSenderService {

  /**
   * Mail manager.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $mailManager;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;


  /**
   * AccountProxy for current user.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * EntityTypeManager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Allowed chars for random string.
   *
   * @var string
   */
  protected $allowedChars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

  /**
   * EmalSenderService constructor.
   *
   * @param \Drupal\Core\Mail\MailManager $mailManager
   *   Mail manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $loggerFactory
   *   Logger factory.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Request.
   * @param \Drupal\Core\Session\AccountProxy $currentUser
   *   Current user.
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Entity type manager.
   */
  public function __construct(MailManager $mailManager,
                              LoggerChannelFactory $loggerFactory,
                              RequestStack $request,
                              AccountProxy $currentUser,
                              EntityTypeManager $entityTypeManager) {
    $this->mailManager = $mailManager;
    $this->loggerFactory = $loggerFactory;
    $this->request = $request;
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Sends verification mail to given email address.
   *
   * @param string $to
   *   Email address where we send mail.
   */
  public function sendVerificationEmail($to = NULL) {
    try {
      /** @var \Drupal\user\Entity\User $user */
      $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
      $code = $this->generate(48);
      if ($to == NULL) {
        $to = $user->getEmail();
      }
      $user->set('field_random_hash', $code);
      $user->save();
      $host = $this->request->getCurrentRequest()->getSchemeAndHttpHost();
      $url = "${host}/user/verify-email?code=${code}";
      $message = "მიყევით მოცემულ ლინკს რათა bitcamp.ge-ზე გაიაროთ ელ.ფოსტის ვერიფიკაცია \n $url";
      $module = 'bcmp_user';
      $key = 'verify_email';
      $params['message'] = $message;
      $langcode = 'en';
      $send = TRUE;

      $result = $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] != TRUE) {
        $message = "ვერ მოხერხდა ელ.ფოსტის გაგზავნა, გთხოვთ ცადოთ თავიდან ან მიმარტოთ ადმინისტრაციას";
      }
      else {
        $message = "ელ.ფოსტა წამრატებით გაიგზავნა შემდეგ მისამართზე: ${to} გთხოვთ მიჰყვეთ ელ.ფოსტაზე გამოგზავნილ ინსტრუქციას";
      }
      return $message;
    }
    catch (\Exception $e) {

    }
  }

  /**
   * Generates random string.
   */
  public function generate(int $length = 10): string {
    // The maximum integer we want from random_int().
    $max = strlen($this->allowedChars) - 1;
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
      $pass .= $this->allowedChars[random_int(0, $max)];
    }
    return $pass;
  }

}
