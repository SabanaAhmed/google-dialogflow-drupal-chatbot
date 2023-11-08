> ```
>
>  Chatbot - The easiest way to build powerful bots and chatbot
>  By integrated with google dialogflow
>
> ```

### 1 - INTRODUCTION

This module is a integration with google cloud dialog flow service that help you to make an chatbot
conversation.


### 2 - INSTALLATION

Install require google dialogflow package
```bash
composer require google/cloud-dialogflow
```
Enable the module. Go to admin/config/chatbot and set a valid project id and secret json
key that provided by dialogflow.

A Block is created with the custom template. You can go to the Block system page in
Drupal and set visiblity contenxt of your block.

And that's it, a fixed widget of chatbot would apper in the specified pages.


### 3 - CONFIGURATIONS

Please go to /admin/config/chatbot to explore the configuration options of the
widget.

### 4 - UNINSTALL

Just uninstall the module since the connector.
Remove google package.
```bash
composer remove google/cloud-dialogflow
```


### 5 - REQUERIMENTS

This module needs an install google dialogflow package.
```bash
composer require google/cloud-dialogflow
```

## API Reference

```http
  POST /chatbot.php
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `msg`      | `string` | **Required**. Msg input of user |

#### call_server(msg)
`/assets/js/chatbot.js`.

Takes string text and returns the dialogflow response.

```json
{
"data":{
        "message": "Hi,How can i help you?",
        "intent": "Welcome"
      },
      "method": "POST",
      "status": 200
}
```
