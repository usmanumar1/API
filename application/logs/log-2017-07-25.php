<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-07-25 11:12:55 --> Query error: Unknown column 'answer.answer_id' in 'field list' - Invalid query: SELECT `answer`.`answer_id`, `answer`.`question_id`
FROM `question`
JOIN `question_answer` ON `question`.`question_id` = `question_answer`.`question_id`
WHERE `answer`.`doctor_id` = 1
ERROR - 2017-07-25 11:12:55 --> Severity: Error --> Call to a member function result_array() on boolean G:\xampp\htdocs\MVP\MVP-API\application\models\Question_model.php 38
ERROR - 2017-07-25 11:13:21 --> Query error: Unknown column 'answer.answer_id' in 'field list' - Invalid query: SELECT `answer`.`answer_id`, `answer`.`question_id`
FROM `question`
JOIN `question_answer` ON `question`.`question_id` = `question_answer`.`question_id`
WHERE `answer`.`doctor_id` = 1
ERROR - 2017-07-25 11:13:21 --> Severity: Error --> Call to a member function result_array() on boolean G:\xampp\htdocs\MVP\MVP-API\application\models\Question_model.php 38
ERROR - 2017-07-25 11:46:39 --> Severity: Notice --> Undefined property: stdClass::$token G:\xampp\htdocs\MVP\MVP-API\application\controllers\Doctor.php 40
ERROR - 2017-07-25 11:46:39 --> Severity: Warning --> date_format() expects parameter 1 to be DateTimeInterface, boolean given G:\xampp\htdocs\MVP\MVP-API\application\controllers\Doctor.php 56
ERROR - 2017-07-25 11:47:31 --> Severity: Notice --> Undefined property: stdClass::$token G:\xampp\htdocs\MVP\MVP-API\application\controllers\Doctor.php 40
ERROR - 2017-07-25 11:47:43 --> Severity: Notice --> Undefined property: stdClass::$token G:\xampp\htdocs\MVP\MVP-API\application\controllers\Doctor.php 40
ERROR - 2017-07-25 11:49:03 --> Severity: Warning --> date_format() expects parameter 1 to be DateTimeInterface, string given G:\xampp\htdocs\MVP\MVP-API\application\controllers\Doctor.php 56
