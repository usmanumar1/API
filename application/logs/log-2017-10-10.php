<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-10-10 09:06:56 --> Query error: Table 'mvp.chat_response' doesn't exist - Invalid query: SELECT *
FROM `chat_response`
WHERE `doctor_id` = '1'
ORDER BY `response_time` DESC
 LIMIT 50
ERROR - 2017-10-10 09:06:56 --> Severity: Error --> Call to a member function result_array() on boolean E:\USMAN\xammp\htdocs\MVP-API-FINAL\application\models\Doctor_model.php 41
ERROR - 2017-10-10 10:25:51 --> Query error: Unknown column 'admin_role' in 'field list' - Invalid query: SELECT `admin_role`
FROM `admin`
WHERE `admin_name` = 'harry2'
ERROR - 2017-10-10 10:25:51 --> Severity: Error --> Call to a member function num_rows() on boolean E:\USMAN\xammp\htdocs\MVP-API-FINAL\application\models\Admin_model.php 35
ERROR - 2017-10-10 10:27:48 --> Query error: Unknown column 'admin_role' in 'field list' - Invalid query: SELECT `admin_role`
FROM `admin`
WHERE `admin_name` = 'harry2'
ERROR - 2017-10-10 10:27:48 --> Severity: Error --> Call to a member function num_rows() on boolean E:\USMAN\xammp\htdocs\MVP-API-FINAL\application\models\Admin_model.php 35
ERROR - 2017-10-10 10:27:51 --> Query error: Unknown column 'admin_role' in 'field list' - Invalid query: SELECT `admin_role`
FROM `admin`
WHERE `admin_name` = 'harry2'
ERROR - 2017-10-10 10:27:51 --> Severity: Error --> Call to a member function num_rows() on boolean E:\USMAN\xammp\htdocs\MVP-API-FINAL\application\models\Admin_model.php 35
ERROR - 2017-10-10 22:08:15 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '' at line 1 - Invalid query: SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(time_answered))) as result FROM question_answer where doctor_id =
ERROR - 2017-10-10 22:08:15 --> Severity: Error --> Call to a member function row_array() on boolean E:\USMAN\xammp\htdocs\MVP-API-FINAL\application\models\Doctor_model.php 69
ERROR - 2017-10-10 22:17:51 --> Severity: error --> Exception: cURL error 28: Connection timed out after 10000 milliseconds E:\USMAN\xammp\htdocs\MVP-API-FINAL\vendor\rmccue\requests\library\Requests\Transport\cURL.php 422
ERROR - 2017-10-10 22:20:14 --> Severity: error --> Exception: cURL error 28: Operation timed out after 0 milliseconds with 0 out of 0 bytes received E:\USMAN\xammp\htdocs\MVP-API-FINAL\vendor\rmccue\requests\library\Requests\Transport\cURL.php 422
ERROR - 2017-10-10 22:20:34 --> Severity: error --> Exception: cURL error 28: Connection timed out after 10000 milliseconds E:\USMAN\xammp\htdocs\MVP-API-FINAL\vendor\rmccue\requests\library\Requests\Transport\cURL.php 422
