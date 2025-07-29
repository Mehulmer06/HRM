<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hook['post_controller'] = [
    'class'    => 'Activity_hook',
    'function' => 'log_activity',
    'filename' => 'Activity_hook.php',
    'filepath' => 'hooks'
];
