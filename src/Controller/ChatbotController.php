<?php

namespace Drupal\chatbot\Controller;

// use Google\Cloud\Dialogflow\V2\SessionsClient;
// use Google\Cloud\Dialogflow\V2\TextInput;
// use Google\Cloud\Dialogflow\V2\QueryInput;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Google\Client;


/**
 * Returns responses for chatbot routes.
 */
class ChatbotController extends ControllerBase
{

  /**
   * Get the access token to get conversation started with chatbot dialogflow.
   */
  public function chatToken()
  {
    $client = new Client();
    $client->setAuthConfig($this->getClientSecretKey());
    $client->addScope("https://www.googleapis.com/auth/cloud-platform");
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();
    $response = [
      'token' => $token
    ];
    return new JsonResponse($response);

  }

  /**
   * Get client secret json file path.
   * @return string|bool
   */
  private function getClientSecretKey()
  {
    $clientSecretKeyJson = \Drupal::config('chatbot.settings')->get('chatbot.client_secret');
    if ($clientSecretKeyJson) {
      return  json_decode($clientSecretKeyJson,true);
    }
    return NULL;
  }

}
