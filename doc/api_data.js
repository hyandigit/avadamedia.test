account
catalog
info
reference
shop
api.yaml
favicon-16x16.png
favicon-32x32.png
index.html
index.php
oauth2-redirect.html
swagger-ui.css
swagger-ui.js
swagger-ui-bundle.js
swagger-ui-standalone-preset.jsdefine({ "api": [
  {
    "type": "get",
    "url": "/ticket/all/:film",
    "title": "Получить список купленных или забронированных билетов",
    "name": "GetTicket",
    "group": "Ticket",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "film",
            "description": "<p>Идентификатор фильма</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n   [\n           {\n           \"ID\": 2,\n           \"row\": 1,\n           \"col\": 1,\n           \"option\": 1,\n           \"films_ID\": 1\n           }\n       ]",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 Ok\n{\n  []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "controllers/TicketController.php",
    "groupTitle": "Ticket"
  }
] });
