<?php

namespace Drupal\chatbot\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure chatbot settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Construct method.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Create method.
   */
  public static function create(ContainerInterface $container) {
    // SET DEPENDENCY INJECTION.
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'chatbot_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['chatbot.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('chatbot.settings');

    $fileValidators = [
      'file_validate_extensions' => ['json'],
    ];

    $form['chatbot_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Chatbot Title'),
      '#default_value' => $config->get('chatbot.chatbot_title'),
    ];

    $form['project_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Project ID'),
      '#description' => $this->t('Set your-project-id with your Dialogflow project ID'),
      '#default_value' => $config->get('chatbot.project_id'),
      '#required' => TRUE,
    ];

    $form['client_secret'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Client Secret Key'),
      '#description' => $this->t('Client secret JSON key value for your v2 Dialogflow agent.'),
      '#default_value' => $config->get('chatbot.client_secret'),
      '#required' => TRUE,
    ];

    $form['chatbot_logo'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Alternative Logo'),
      '#description' => $this->t('Ideal size 46x46'),
      '#default_value' => $config->get('chatbot.chatbot_logo'),
      '#upload_location' => 'public://chatbot/',
      '#required' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('project_id') == '') {
      $form_state->setErrorByName('project_id', $this->t('The value can not be empty.'));
    }

    if ($form_state->getValue('client_secret') == '') {
      $form_state->setErrorByName('client_secret', $this->t('The value can not be empty.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('chatbot.settings');
    $config->set('chatbot.chatbot_title', $form_state->getValue('chatbot_title'));
    $config->set('chatbot.project_id', $form_state->getValue('project_id'));
    $config->set('chatbot.client_secret', $form_state->getValue('client_secret'));

    // Saving logo if uploaded.
    if (!empty($form_state->getValue('chatbot_logo'))) {
      $fid = reset($form_state->getValue('chatbot_logo'));
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      $file->setPermanent();
      $file->save();
      $config->set('chatbot.chatbot_logo', $form_state->getValue('chatbot_logo'));
    }
    else {
      $config->set('chatbot.chatbot_logo', FALSE);
    }


    $config->save();

    drupal_flush_all_caches();
    parent::submitForm($form, $form_state);
  }

  /**
   * Returns value of specific form element.
   */
  private function getValue($form_state, $prop_name) {
    return trim($form_state->getValue($prop_name));
  }

}
