<?php

namespace Drupal\bcmp_user;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
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
   * Private temp store.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStore;

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
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $tempStoreFactory
   *   Private temp store factory.
   */
  public function __construct(MailManager $mailManager,
                              LoggerChannelFactory $loggerFactory,
                              RequestStack $request,
                              AccountProxy $currentUser,
                              EntityTypeManager $entityTypeManager,
                              PrivateTempStoreFactory $tempStoreFactory) {
    $this->mailManager = $mailManager;
    $this->loggerFactory = $loggerFactory;
    $this->request = $request;
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
    $this->tempStore = $tempStoreFactory->get('bcmp_user');
  }

  /**
   * Sends verification mail to given email address.
   *
   * @param string $to
   *   Email address where we send mail.
   */
  public function sendVerificationEmail($to, $code) {
    try {
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
        $data = [
          'code' => 400,
          'message' =>
          'ვერ მოხერხდა ელ.ფოსტის გაგზავნა, გთხოვთ ცადოთ თავიდან ან მიმართოთ ადმინისტრაციას',
        ];
      }
      else {
        $data = [
          'code' => 200,
          'message' =>
          "ვერიფიკაციის ბმული გამოგზავნილია ${to} - ზე გთხოვთ მიჰყვეთ მას.",
        ];
        $this->tempStore->set('email_sent', TRUE);
      }
      return $data;
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
