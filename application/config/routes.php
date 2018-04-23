<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//----------------------------------------------MVP - DASHBOARD--------------------------------------------------------

//------LOGIN
$route['api/loginAdmin'] = 'Admin/loginAdmin'; //login

//-------------------Question----------------------
$route['api/questions'] = 'Question/getAllQuestions'; //Getting all questions for dashboard
$route['api/questionsnew'] = 'Question/getAllQuestionsNew'; //Getting all questions for dashboard
$route['api/questionanswered'] = 'Question/getAllQuestionsAnswered'; //Getting all questions for dashboard
$route['api/questionuploaded'] = 'Question/getAllQuestionsUploaded'; //Getting all questions for dashboard
$route['api/questionsassigned'] = 'Question/getAllQuestionsAssigned'; //Getting all questions for dashboard
$route['api/questionsfaq'] = 'Question/getAllQuestionsFaq'; //Getting all questions for dashboard
$route['api/questionsignoredetc'] = 'Question/getAllQuestionsIgnoredEtc'; //Getting all questions for dashboard
$route['api/question/transcription'] = 'Question/getAllQuestionsTranscription'; //Getting all questions for dashboard with transcription
//-------------------Story----------------------
$route['api/stories'] = 'Story/getAllStories'; //Getting all stories for dashboard
$route['api/storiesNew'] = 'Story/getAllStoriesNew'; //Getting all New stories for dashboard
$route['api/storiesApproved'] = 'Story/getAllStoriesApproved'; //Getting all Approved stories for dashboard
$route['api/storiesRejected'] = 'Story/getAllStoriesRejected'; //Getting all Approved stories for dashboard

$route['api/stories/transcription'] = 'Story/getAllStoriesTranscription'; //Getting all stories for dashboard with transcription

//-------------------Comment----------------------
$route['api/comments'] = 'Story/getComments'; //Getting all stories for dashboard

//-------------------Answer----------------------
$route['api/answers'] = 'Answer/getAllAnswers'; //Getting all answers for dashboard

//-------------------Doctor----------------------
$route['api/doctors'] = 'Doctor/getAllDoctors'; //Getting all doctors for dashboard
$route['api/new/doctorsSignup'] = 'Doctor/getnewDoctorsSignup'; //Getting all new  doctors for dashboard
$route['api/resetPassword'] = 'Doctor/resetPassword'; //Getting all new  doctors for dashboard
$route['api/resetPin'] = 'Doctor/resetPin'; //Getting all new  doctors for dashboard
$route['api/doctor'] = 'Doctor/getDoctor'; //Getting doctor dashboard
$route['api/doctor/help'] = 'Doctor/getAllChatResponses'; //Getting responses

//-------------------User----------------------
$route['api/users'] = 'User/getAllUsers'; //Getting all users
$route['api/user'] = 'User/getUser'; //Getting single user
$route['api/user/questions'] = 'User/getUserQuestions'; //Getting single user questions
$route['api/user/stories'] = 'User/getUserStories'; //Getting single user stories
$route['api/user/feedback'] = 'User/getUserFeedback'; //Getting single user feedback

//-------------------Feedback----------------------
$route['api/feedback'] = 'Feedback/getAllFeedback'; //Getting all feedback
$route['api/feedback/transcription'] = 'Feedback/getAllFeedbackTranscription'; //Getting all feedback transcription