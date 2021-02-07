<?php

namespace Drupal\bcmp_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a BitCamp User form.
 */
class PasswordForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bcmp_user_password';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('პაროლი'),
      '#required' => TRUE,
    ];
    $form['password_confirm'] = [
      '#type' => 'password',
      '#title' => $this->t('გაიმეორეთ პაროლი '),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('გაგზავნა'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (mb_strlen($form_state->getValue('password')) < 6) {
      $form_state->setErrorByName('length', $this->t('პაროლი მინიმუმ 6 სიმბოლოსგან უნდა შედგებოდეს'));
    }
    if($form_state->getValue('password') != $form_state->getValue('password_confirm')) {
      $form_state->setErrorByName('mismatch', $this->t('გამეორებული პაროლი არ ემთხვევა მითითებულ პაროლს'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $userUtilServices = \Drupal::service('bcmp_users.util_services');
    $userUtilServices->setUserPassword($form_state->getValue('password'));
    $this->messenger()->addStatus($this->t('პაროლი წარმატებით დაყენდა'));
    $form_state->setRedirect('user.page');
  }

}
