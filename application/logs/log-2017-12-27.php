<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-12-27 12:31:12 --> Severity: Notice --> Trying to get property of non-object E:\USMAN\xammp\htdocs\MVP-API-NEW\application\controllers\Question.php 223
ERROR - 2017-12-27 12:31:12 --> Severity: Notice --> Trying to get property of non-object E:\USMAN\xammp\htdocs\MVP-API-NEW\application\controllers\Question.php 224
ERROR - 2017-12-27 12:31:12 --> Query error: Undeclared variable: offset - Invalid query: SELECT * FROM `question` as q left join question_answer as qa on q.question_id = qa.question_id where q.question_action = 2 And qa.faq !=0 limit  offset  
ERROR - 2017-12-27 12:31:12 --> Severity: Error --> Call to a member function result_array() on boolean E:\USMAN\xammp\htdocs\MVP-API-NEW\application\models\Question_model.php 52
ERROR - 2017-12-27 17:09:54 --> The upload path does not appear to be valid.
ERROR - 2017-12-27 13:47:21 --> Query error: Unknown column 'q.doctor_id' in 'on clause' - Invalid query: SELECT * FROM `question` as q inner join doctor as d on q.doctor_id = d.doc_id  where q.answer_status =1 limit 10 offset 0 
ERROR - 2017-12-27 13:47:21 --> Severity: Error --> Call to a member function result_array() on boolean E:\USMAN\xammp\htdocs\MVP-API-NEW\application\models\Question_model.php 47
ERROR - 2017-12-27 16:51:14 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'group by q.question_id desc' at line 1 - Invalid query: SELECT * FROM `question_answer` as qa inner join doctor as d on qa.doctor_id = d.doc_id inner join question as q on q.question_id = qa.question_id where q.answer_status =1 And qa.approve_status = 1 And q.doc_answer_status = 1 limit 10 offset 0 group by q.question_id desc 
ERROR - 2017-12-27 16:51:14 --> Severity: Error --> Call to a member function result_array() on boolean E:\USMAN\xammp\htdocs\MVP-API-NEW\application\models\Question_model.php 47
ERROR - 2017-12-27 22:20:10 --> The upload path does not appear to be valid.
ERROR - 2017-12-27 22:25:12 --> The upload path does not appear to be valid.
ERROR - 2017-12-27 22:25:38 --> The upload path does not appear to be valid.
ERROR - 2017-12-27 22:26:13 --> The upload path does not appear to be valid.
ERROR - 2017-12-27 22:26:51 --> The upload path does not appear to be valid.
