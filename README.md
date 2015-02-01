Questions Tool
==================

The question and answer tool is a web application written in PHP that allows anyone with a web browser and connection to the server to propose and vote on questions on a particular subject.  A question tool author can create an instance with a specific topic, after which web users can propose questions.  Once the question is submitted, users can then vote on the question.  Questions that are more popular are pushed to the top.  Users can also comment on the questions.

Admins of the instance have various special privileges, eg can mute or delete certain questions, suspend the instance, etc.

## Requirements

* PHP
* MySQL
* Webserver capable of executing PHP code

## Installation

* To customize for use, you need to update variables in functions.php

1. line 11 needs:
  db.hostname.or.ip replaced by the database host (mysql)
  db.user replaced by the database user with access to the choosen database
  db.user.pw replaced by the password for the database user
2. line 14 needs db.name replaced by the database name
3. line 265 needs web.hostname replaced by the hostname of the running web server or virtual host

* You also need to add the following to your apache config to support unique urls for instances:

```
RewriteEngine on
RewriteCond %{REQUEST_URI} /questions.*
RewriteRule !\.(gif|jpg|php|css)$ /questions/list.php [L] 
```
