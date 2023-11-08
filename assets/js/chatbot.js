(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.cfcChatbot = {
    attach: function (context, settings) {
      $('main', context).once('cfcChatbot').each(function () {
        // Handling chatbot logo and user avatar.
        var logos = {};

        if (drupalSettings.chatbot.chatbot_logo) {
          logos['chatbotLogo']=drupalSettings.chatbot.chatbot_logo;
        }

        if (drupalSettings.chatbot.chatbot_title) {
          logos['chatbotTitle']=drupalSettings.chatbot.chatbot_title;
        }

        chatbotResize();
        var accessToken = getAuthToken();

        var sessionId = Math.random();

        var INDEX = 0;

        $( window ).resize(function() {
          chatbotResize();
        });

        function getAuthToken()
        {
          $.ajax({
            method: "POST",
            url: drupalSettings.chatbot.base_url+"/chatToken",
            dataType: "json",
            success: function(response) {
              accessToken = response.token.access_token;
            }
          });
        }

        function chatbotResize(){
          var chatbotContainer = $(".chat-box");
          var heightChatbotHead = chatbotContainer.find('.chat-box-header').outerHeight();
          heightChatbotHead = heightChatbotHead > 0 ? heightChatbotHead : 78;
          var heightChatbotQuery = chatbotContainer.find('.chat-input').outerHeight();
          heightChatbotQuery = heightChatbotQuery > 0 ? heightChatbotQuery : 81;
          var chatlogs = chatbotContainer.find('.chat-logs');
          var calcHeight = $(window).height() - (heightChatbotHead + heightChatbotQuery) - 30;
          chatlogs.height(calcHeight);
        }

        $("#chat-submit").click(function(e) {
          e.preventDefault();
          var userMsg = $("#chat-input").val();
          if(userMsg.trim() == ''){
            return false;
          }
          generate_message(userMsg, 'self');
          // Call api server.
          call_server(userMsg);
        });

        function call_server(userInputMsg) {

          $.ajax({
            type: "POST",
            url: "https://dialogflow.googleapis.com/v2/projects/ceschatbot-dbjwvn/agent/sessions/"+sessionId+":detectIntent",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + accessToken,
            },
            data: JSON.stringify({ "queryInput":{
                "text":{
                    "text": userInputMsg,
                    "languageCode":"en"
                }
            } }),
            success: function(data) {
                console.log(data.queryResult.fulfillmentText);
                generate_message(data.queryResult.fulfillmentText, 'user');
            },
            error: function() {
                console.log("Internal Server Error");
            }
        });
        }

        function generate_message(msg, type) {
          INDEX++;
          var str=``;
          var img = `<img src="${logos['chatbotLogo']}">`;
          if(type == 'self') {
            str += `<div id='cm-msg-${INDEX}' class="chat-msg ${type}">
            <div class="cm-msg-text"><span class="name-user">You:</span>${msg}</div>
                </div>`;
          }
          else {
          str += `<div id='cm-msg-${INDEX}' class="chat-msg ${type}">
                   <div class="cm-msg-text"><span class="name-user">${logos['chatbotTitle']}:</span>${msg}</div>
                </div>`;
          }
          $(".chat-logs").append(str);
          $("#cm-msg-"+INDEX).hide().fadeIn(300);
          if(type == 'self'){
            $("#chat-input").val('');
          }
          $(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);

        }

        // Greeting Message.
        function welcome_message() {
          var welcomeMsg = "Welcome, how can I help you? ";
          setTimeout(function() {
            generate_message(welcomeMsg, 'user');
          }, 1000);
        }

        $(document).delegate(".chat-btn", "click", function() {
          var value = $(this).attr("chat-value");
          var name = $(this).html();
          $("#chat-input").attr("disabled", false);
          generate_message(name, 'self');
        });

        // Open chatbot dialog.
        $("#chat-circle").click(function() {
          welcome_message();
          $("#chat-circle").toggle('scale');
          $(".chat-box").toggle('scale');
        });

        // Close chatbot dialog.
        $(".chat-box-toggle").click(function() {
          $("#chat-circle").toggle('scale');
          $(".chat-box").toggle('scale');
        });

      });
    }
  }
})(jQuery, Drupal, drupalSettings);
