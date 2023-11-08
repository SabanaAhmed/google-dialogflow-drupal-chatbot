<?php

namespace Drupal\chatbot\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "chatbot_block",
 *   admin_label = @Translation("Chatbot"),
 *   category = @Translation("Containers for Change")
 * )
 */
class ChatbotBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Configuration state Drupal Site.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;
  /**
   * File Usage serivce.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Construct method.
   */
  public function __construct(ConfigFactory $configFactory, EntityTypeManager $entity_type_manager) {
    $this->configFactory = $configFactory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Create method.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $request = \Drupal::request();
    $base_url = $request->getSchemeAndHttpHost() . $request->getBaseUrl();
    $config = $this->configFactory->getEditable('chatbot.settings');
    $ChatbotSettings = [
      'chatbot_title' => $config->get('chatbot.chatbot_title'),
      'base_url' => $base_url,
    ];
    // If alternative logo is set.
    $altLogo = $config->get('chatbot.chatbot_logo');
    if ($altLogo) {
      $file = $this->entityTypeManager->getStorage('file')->load($altLogo[0]);
      $ChatbotSettings['chatbot_logo'] = $file ? file_create_url($file->getFileUri()) : '';
    }


    // Attach settings and libraries to the block.
    return [
      '#type' => 'html',
      '#theme' => 'chatbot',
      '#variables' => [
        'chatbot_title' => $config->get('chatbot.chatbot_title'),
        'chatbot_logo' => $ChatbotSettings['chatbot_logo'],
        'lang' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      ],
      '#attached' => [
        'library' => [
          'chatbot/chatbot_assets',
        ],
        'drupalSettings' => [
          'chatbot' => $ChatbotSettings,
        ],
      ],
    ];
  }

}
