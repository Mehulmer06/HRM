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
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['shrm/login'] = 'shrm_controllers/LoginController/index';
$route['shrm/login/captcha'] = 'shrm_controllers/LoginController/captcha';
$route['shrm/login/authenticate'] = 'shrm_controllers/LoginController/authenticate';
$route['shrm/logout'] = 'shrm_controllers/LoginController/logout';
$route['shrm/dashboard'] = 'shrm_controllers/DashboardController/index';
$route['shrm/test'] = 'shrm_controllers/DashboardController/test';

$route['project-staff'] = 'shrm_controllers/ProjectStaffController/index';
$route['project-staff/create'] = 'shrm_controllers/ProjectStaffController/create';
$route['project-staff/store'] = 'shrm_controllers/ProjectStaffController/store';
$route['project-staff/edit/(:num)'] = 'shrm_controllers/ProjectStaffController/edit/$1';
$route['project-staff/update/(:num)'] = 'shrm_controllers/ProjectStaffController/update/$1';
$route['project-staff/toggle-status'] = 'shrm_controllers/ProjectStaffController/toggle_status';
$route['project-staff/show/(:num)'] = 'shrm_controllers/ProjectStaffController/show/$1';
$route['project-staff/renewal-contract/(:num)'] = 'shrm_controllers/ProjectStaffController/renewContract/$1';


$route['holiday'] = 'shrm_controllers/HolidayController/index';
$route['holiday/(:num)'] = 'shrm_controllers/HolidayController/index/$1';

$route['work-progress'] = 'shrm_controllers/EvaluationController/index';
$route['work-progress/create'] = 'shrm_controllers/EvaluationController/create';
$route['work-progress/store'] = 'shrm_controllers/EvaluationController/store';
$route['work-progress/edit/(:any)'] = 'shrm_controllers/EvaluationController/edit/$1';
$route['work-progress/update/(:num)'] = 'shrm_controllers/EvaluationController/update/$1';
$route['work-progress/view/(:any)'] = 'shrm_controllers/EvaluationController/show/$1';
$route['work-progress/update_status'] = 'shrm_controllers/EvaluationController/update_status';


$route['work-progress/comments/(:any)'] = 'shrm_controllers/EvaluationController/comments/$1';
$route['work-progress/add_comment'] = 'shrm_controllers/EvaluationController/add_comment';
$route['work-progress/get_comments'] = 'shrm_controllers/EvaluationController/get_comments';
$route['work-progress/report/(:any)'] = 'shrm_controllers/EvaluationController/report/$1';

$route['note'] = 'shrm_controllers/NoteController/index';
$route['note/create'] = 'shrm_controllers/NoteController/create';
$route['note/store'] = 'shrm_controllers/NoteController/store';
$route['note/edit/(:num)'] = 'shrm_controllers/NoteController/edit/$1';
$route['note/update/(:num)'] = 'shrm_controllers/NoteController/update/$1';
$route['note/remove-attachment'] = 'shrm_controllers/NoteController/remove_attachment';
$route['note/close'] = 'shrm_controllers/NoteController/close';
$route['note/delete'] = 'shrm_controllers/NoteController/delete';
$route['note/view/(:num)'] = 'shrm_controllers/NoteController/view/$1';
$route['note/get-discussions'] = 'shrm_controllers/NoteController/get_discussions';
$route['note/add-discussion'] = 'shrm_controllers/NoteController/add_discussion';
$route['note/take-action'] = 'shrm_controllers/NoteController/take_action';


$route['leave'] = 'shrm_controllers/LeaveController/index';
$route['leave/apply'] = 'shrm_controllers/LeaveController/applyLeave';
$route['leave/get_by_id/(:num)'] = 'shrm_controllers/LeaveController/get_by_id/$1';
$route['leave/update'] = 'shrm_controllers/LeaveController/updateLeave';
$route['leave/take_action'] = 'shrm_controllers/LeaveController/take_action';
$route['leave/delete/(:num)'] = 'shrm_controllers/LeaveController/delete/$1';

$route['leave/cancel'] = 'shrm_controllers/LeaveController/cancel';
$route['leave/cancel_action'] = 'shrm_controllers/LeaveController/cancel_action';
$route['leave/get_cancellation_details/(:num)'] = 'shrm_controllers/LeaveController/get_cancellation_details/$1';

$route['extra-day-requests'] = 'shrm_controllers/ExtraDayController/index';
$route['ro-extra-day-approval'] = 'shrm_controllers/ExtraDayController/ro_approval';

$route['extra-day-requests'] = 'shrm_controllers/ExtraDayRequestController/index';
$route['extra-day-requests/create'] = 'shrm_controllers/ExtraDayRequestController/create';
$route['extra-day-requests/update/(:num)'] = 'shrm_controllers/ExtraDayRequestController/update/$1';
$route['extra-day-requests/delete'] = 'shrm_controllers/ExtraDayRequestController/delete';
$route['extra-day-requests/get/(:num)'] = 'shrm_controllers/ExtraDayRequestController/get_request/$1';

$route['ro-extra-day-approval'] = 'shrm_controllers/ExtraDayRequestController/roIndex';
$route['ro-extra-day-approval/action_request'] = 'shrm_controllers/ExtraDayRequestController/action_request';

$route['request-issue'] = 'shrm_controllers/RequestIssueController/index';
$route['request-issue/store'] = 'shrm_controllers/RequestIssueController/store';
$route['request-issue/show/(:num)'] = 'shrm_controllers/RequestIssueController/show/$1';
$route['request-issue/commentStore'] = 'shrm_controllers/RequestIssueController/commentStore';
$route['request-issue/(:num)'] = 'shrm_controllers/RequestIssueController/fetch/$1';
$route['request-issue/update_status'] = 'shrm_controllers/RequestIssueController/update_status';

$route['shrm/finance'] = 'shrm_controllers/FinanceController/index';
$route['shrm/finance/store'] = 'shrm_controllers/FinanceController/store';

$route['casual-leave'] = 'shrm_controllers/CasualLeaveController/index';
$route['casual-leave/save'] = 'shrm_controllers/CasualLeaveController/save';
$route['casual-leave/get/(:num)'] = 'shrm_controllers/CasualLeaveController/get_grant/$1';
$route['casual-leave/delete/(:num)'] = 'shrm_controllers/CasualLeaveController/delete/$1';

$route['profile'] = 'shrm_controllers/ProfileController/index';
$route['profile/update'] = 'shrm_controllers/ProfileController/update';
$route['update-phone'] = 'shrm_controllers/ProfileController/update_phone';
$route['change-password'] = 'shrm_controllers/ProfileController/changePassword';
$route['change-update'] = 'shrm_controllers/ProfileController/update_password';

// Activity Module Routes
$route['activity'] = 'shrm_controllers/ActivityController/index';
$route['activity/store'] = 'shrm_controllers/ActivityController/store';
$route['activity/update'] = 'shrm_controllers/ActivityController/update';
$route['activity/delete'] = 'shrm_controllers/ActivityController/delete';
$route['activity/restore'] = 'shrm_controllers/ActivityController/restore';
$route['activity/get_activity'] = 'shrm_controllers/ActivityController/get_activity';

$route['project'] = 'shrm_controllers/ProjectController/index';
$route['project/index'] = 'shrm_controllers/ProjectController/index';
$route['project/store'] = 'shrm_controllers/ProjectController/store';
$route['project/update'] = 'shrm_controllers/ProjectController/update';
$route['project/delete'] = 'shrm_controllers/ProjectController/delete';
$route['project/restore'] = 'shrm_controllers/ProjectController/restore';
$route['project/get_project'] = 'shrm_controllers/ProjectController/get_project';

$route['shrm/finance'] = 'shrm_controllers/FinanceController/index';
$route['shrm/finance/store'] = 'shrm_controllers/FinanceController/store';
$route['shrm/finance/store_other_document'] = 'shrm_controllers/FinanceController/store_other_document';

$route['shrm/leave/pfd/(:num)'] = 'shrm_controllers/LeaveController/leavePDF/$1';


$route['work-progress/notifications'] = 'shrm_controllers/EvaluationController/get_notifications';
$route['work-progress/notifications/count'] = 'shrm_controllers/EvaluationController/get_notification_count';
$route['work-progress/notifications/mark-read'] = 'shrm_controllers/EvaluationController/mark_notification_read';
$route['work-progress/notifications/mark-all-read'] = 'shrm_controllers/EvaluationController/mark_all_notifications_read';