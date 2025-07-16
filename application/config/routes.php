<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|	https://codeigniter.com/userguide3/general/routing.html
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

$route['login'] = 'LoginController/index';
$route['login/authenticate'] = 'LoginController/authenticate';
$route['logout'] = 'LoginController/logout';
$route['dashboard'] = 'DashboardController/index';

$route['project-staff'] = 'ProjectStaffController/index';
$route['project-staff/create'] = 'ProjectStaffController/create';
$route['project-staff/store'] = 'ProjectStaffController/store';
$route['project-staff/edit/(:num)'] = 'ProjectStaffController/edit/$1';
$route['project-staff/update/(:num)'] = 'ProjectStaffController/update/$1';
$route['project-staff/toggle-status'] = 'ProjectStaffController/toggle_status';
$route['project-staff/show/(:num)'] = 'ProjectStaffController/show/$1';
$route['project-staff/renewal-contract/(:num)'] = 'ProjectStaffController/renewContract/$1';


$route['holiday'] = 'HolidayController/index';
$route['holiday/(:num)'] = 'HolidayController/index/$1';

$route['work-progress'] = 'EvaluationController/index';
$route['work-progress/create'] = 'EvaluationController/create';
$route['work-progress/store'] = 'EvaluationController/store';
$route['work-progress/edit/(:num)'] = 'EvaluationController/edit/$1';
$route['work-progress/update/(:num)'] = 'EvaluationController/update/$1';
$route['work-progress/view/(:num)'] = 'EvaluationController/show/$1';
$route['work-progress/update_status'] = 'EvaluationController/update_status';


$route['work-progress/comments/(:num)'] = 'EvaluationController/comments/$1';
$route['work-progress/add_comment'] = 'EvaluationController/add_comment';
$route['work-progress/get_comments'] = 'EvaluationController/get_comments';

$route['note'] = 'NoteController/index';
$route['note/create'] = 'NoteController/create';
$route['note/store'] = 'NoteController/store';
$route['note/edit/(:num)'] = 'NoteController/edit/$1';
$route['note/update/(:num)'] = 'NoteController/update/$1';
$route['note/remove-attachment'] = 'NoteController/remove_attachment';
$route['note/close'] = 'NoteController/close';
$route['note/delete'] = 'NoteController/delete';
$route['note/view/(:num)'] = 'NoteController/view/$1';
$route['note/get-discussions'] = 'NoteController/get_discussions';
$route['note/add-discussion'] = 'NoteController/add_discussion';
$route['note/take-action'] = 'NoteController/take_action';


// ✅ MAIN LEAVE MANAGEMENT ROUTES
$route['leave'] = 'LeaveController/index';
$route['leave/apply'] = 'LeaveController/applyLeave';
$route['leave/get_by_id/(:num)'] = 'LeaveController/get_by_id/$1';
$route['leave/update'] = 'LeaveController/updateLeave';
$route['leave/take_action'] = 'LeaveController/take_action';
$route['leave/delete/(:num)'] = 'LeaveController/delete/$1';

// ✅ LEAVE CANCELLATION ROUTES
$route['leave/cancel'] = 'LeaveController/cancel';
$route['leave/cancel_action'] = 'LeaveController/cancel_action';
$route['leave/get_cancellation_details/(:num)'] = 'LeaveController/get_cancellation_details/$1';

// ✅ EXTRA DAY ROUTES (if needed)
$route['extra-day-requests'] = 'ExtraDayController/index';
$route['ro-extra-day-approval'] = 'ExtraDayController/ro_approval';

$route['extra-day-requests'] = 'ExtraDayRequestController/index';
$route['extra-day-requests/create'] = 'ExtraDayRequestController/create';
$route['extra-day-requests/update/(:num)'] = 'ExtraDayRequestController/update/$1';
$route['extra-day-requests/delete'] = 'ExtraDayRequestController/delete';
$route['extra-day-requests/get/(:num)'] = 'ExtraDayRequestController/get_request/$1';

$route['ro-extra-day-approval'] = 'ExtraDayRequestController/roIndex';
$route['ro-extra-day-approval/action_request'] = 'ExtraDayRequestController/action_request';


$route['request-issue'] = 'RequestIssueController/index';
$route['request-issue/store'] = 'RequestIssueController/store';
$route['request-issue/show/(:num)'] = 'RequestIssueController/show/$1';
$route['request-issue/commentStore'] = 'RequestIssueController/commentStore';
$route['request-issue/(:num)'] = 'RequestIssueController/fetch/$1';
$route['request-issue/update_status'] = 'RequestIssueController/update_status';


$route['finance'] = 'FinanceController/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
