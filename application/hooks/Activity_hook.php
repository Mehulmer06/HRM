<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_hook {
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        // 1) Load and configure the shrm DB
        $this->CI->shrm = $this->CI->load->database('shrm', TRUE);

        // Ensure query logging is on for shrm
        // (also verify in database.php that 'save_queries' => TRUE for shrm)
        $this->CI->shrm->save_queries = TRUE;

        // load session, user_agent and URL helper
        $this->CI->load->library(['session','user_agent']);
        $this->CI->load->helper('url');

        // start PHP session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Runs after controller method finishes.
     * Logs the request, deriving table_name from the last shrm INSERT/UPDATE/DELETE.
     */
    public function log_activity()
    {
        // bail if shrm DB didn’t load
        if (! isset($this->CI->shrm) || ! $this->CI->shrm) {
            return;
        }

        // build the activity_type string
        $activity_type = strtolower($this->CI->router->fetch_class())
            .'/'
            .strtolower($this->CI->router->fetch_method());

        // skip list
        $skip = [
            'evaluationcontroller/get_notification_count',
        ];
        if (in_array($activity_type, $skip, true)) {
            return;
        }

        // 2) inspect shrm query log
        $queries = $this->CI->shrm->queries;  // array of SQL strings
        $table   = null;

        // scan backwards for the last write
        for ($i = count($queries) - 1; $i >= 0; $i--) {
            $sql = $queries[$i];
            if (preg_match('/\bINSERT\s+INTO\s+`?(\w+)`?/i', $sql, $m)
                || preg_match('/\bUPDATE\s+`?(\w+)`?/i',    $sql, $m)
                || preg_match('/\bDELETE\s+FROM\s+`?(\w+)`?/i', $sql, $m))
            {
                $table = $m[1];
                break;
            }
        }

        // fallback to controller name
        if (empty($table)) {
            $table = strtolower($this->CI->router->fetch_class());
        }

        // map HTTP verbs → actions
        $methodMap = [
            'GET'    => 'ACCESS',
            'POST'   => 'CREATE',
            'PUT'    => 'UPDATE',
            'DELETE' => 'DELETE',
        ];
        $verb   = strtoupper($_SERVER['REQUEST_METHOD']);
        $action = $methodMap[$verb] ?? $verb;

        // if POST has an “id”, treat as UPDATE
        if ($verb === 'POST' && $this->CI->input->post('id')) {
            $action = 'UPDATE';
        }

        // determine record_id
        $id = $this->CI->input->post('id')
            ?: (int)$this->CI->uri->segment(3)
                ?: null;

        // assemble log row
        $log = [
            'user_id'       => (int)$this->CI->session->userdata('user_id'),
            'activity_type' => $activity_type,
            'table_name'    => $table,
            'record_id'     => $id,
            'action'        => $action,
            'description'   => $activity_type,
            'ip_address'    => $this->CI->input->ip_address(),
            'user_agent'    => substr($this->CI->agent->agent_string(), 0, 500),
            'url'           => current_url(),
            'module'        => strtolower($this->CI->router->fetch_class()),
            'session_id'    => session_id(),
            'status'        => 'SUCCESS',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // insert into your audit table if it exists
        if ($this->CI->shrm->table_exists('user_activity_logs')) {
            $this->CI->shrm->insert('user_activity_logs', $log);
        }
    }
}
