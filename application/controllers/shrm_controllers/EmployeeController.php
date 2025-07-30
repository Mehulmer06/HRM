<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EmployeeController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Default DB connection
        $this->load->database();
        // Secondary SHRMDATABASE connection
        $this->shrm = $this->load->database('shrm', TRUE);
        // Load security helper for XSS cleaning
        $this->load->helper('security');
    }

    /**
     * Read uploads/project_staff.xlsx, sanitize, skip existing users, insert new,
     * insert into contract_details, and log operations.
     */
    public function import_file()
    {
        $source = FCPATH . 'uploads/project_staff.xlsx';
        if (!file_exists($source)) {
            show_error("Source file not found: {$source}");
        }

        // Open XLSX as ZIP
        $zip = new ZipArchive;
        if ($zip->open($source) !== TRUE) {
            show_error("Unable to open XLSX file: {$source}");
        }

        // Read shared strings
        $strings = [];
        if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($idx));
            foreach ($xml->si as $si) {
                $strings[] = (string)$si->t;
            }
        }

        // Extract data rows
        $rows = [];
        if (($idx = $zip->locateName('xl/worksheets/sheet1.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($idx));
            foreach ($xml->sheetData->row as $r) {
                $rowIndex = (int)$r['r'];
                if ($rowIndex === 1) continue; // header
                $cells = [];
                foreach ($r->c as $c) {
                    preg_match('/([A-Z]+)/', (string)$c['r'], $m);
                    $col = $m[1];
                    $val = (string)$c->v;
                    if ((string)$c['t'] === 's' && isset($strings[(int)$val])) {
                        $val = $strings[(int)$val];
                    }
                    $cells[$col] = $val;
                }
                $letters = range('A', 'R');  // Extended to include column R
                $rowData = [];
                foreach ($letters as $letter) {
                    $rowData[] = $cells[$letter] ?? '';
                }
                $rows[] = $rowData;
            }
        }
        $zip->close();
        if (empty($rows)) exit("No data rows found in XLSX.\n");

        // Prepare log file
        $timestamp = date('Ymd_His');
        $logPath = FCPATH . "uploads/import_db_log_{$timestamp}.txt";
        $fpLog = fopen($logPath, 'w');
        fwrite($fpLog, "Import Log: " . date('Y-m-d H:i:s') . "\n");

        // Begin transaction
        $this->shrm->trans_start();
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rows as $rowIndex => $row) {
            try {
                // sanitize and pad
                $row = array_pad($row, 18, '');  // Extended to 18 columns (A-R)
                $row = array_map(fn($c) => xss_clean(trim($c)), $row);
                if (count(array_filter($row, fn($v) => $v !== '')) === 0) continue;

                // map columns
                list(
                    $empId, $fullName, $personalEmail, $dobRaw,
                    $gender, $mobile, $pan, $address,
                    $profEmail, $stream, $reportingOfficerName,
                    $designation, $joinRaw, $duration,
                    $salary, $project, $location, $organization
                    ) = $row;

                // Log current row data being processed
                fwrite($fpLog, "[ROW " . ($rowIndex + 2) . "] Processing: EmpID={$empId}, Name={$fullName}, Email={$personalEmail}\n");

                // skip if user exists by employee_id
                $exists = $this->shrm->where('employee_id', $empId)->get('users')->row();
                if ($exists) {
                    fwrite($fpLog, "[SKIP] Employee exists: {$empId}\n");
                    $skipped++;
                    continue;
                }

                // format dates (handle Excel serials)
                $dob = $this->_formatDate($dobRaw);
                $join = $this->_formatDate($joinRaw);

                // lookup reporting officer in default DB using LIKE and select id,name,designation
                $roId = null;
                $roName = $reportingOfficerName;
                $roDesignation = null; // Initialize with null
                if ($reportingOfficerName !== '') {
                    $ro = $this->db->select('id, name, designation')  // Added 'designation' to SELECT
                    ->like('name', $reportingOfficerName)
                        ->get('user')
                        ->row();
                    if ($ro) {
                        $roId = $ro->id;
                        $roName = $ro->name;
                        $roDesignation = $ro->designation;
                    }
                }

                // prepare user data
                $userData = [
                    'name' => ucwords(strtolower($fullName)),
                    'employee_id' => $empId,
                    'dob' => $dob,
                    'email' => $personalEmail,
                    'professional_email' => $profEmail,
                    'password' => md5('12345678'),
                    'address' => $address,
                    'department' => $stream,
                    'gender' => $gender,
                    'pan_number' => $pan,
                    'phone' => $mobile,
                    'reporting_officer_id' => $roId,
                    'reporting_officer_name' => $roName,
                    'reporting_officer_designation' => $roDesignation,
                    'status' => 'Y',
                    'role' => 'employee'
                ];
                $this->shrm->insert('users', $userData);
                $userId = $this->shrm->insert_id();
                fwrite($fpLog, "[INSERT] UserID {$userId}, EmpID {$empId}\n");

                // lookup project in shrm database using LIKE and get project id
                $projectId = null;
                if ($project !== '') {
                    $projectData = $this->shrm->select('id')
                        ->like('project_name', $project)
                        ->get('projects')
                        ->row();
                    if ($projectData) {
                        $projectId = $projectData->id;
                    }
                }

                // calculate end date based on duration
                $endDate = null;
                if ($join && is_numeric($duration) && $duration > 0) {
                    $joinDateTime = new DateTime($join);
                    $joinDateTime->add(new DateInterval('P' . (int)$duration . 'M'));
                    $endDate = $joinDateTime->format('Y-m-d');
                }

                // insert contract details
                $contractData = [
                    'user_id' => $userId,
                    'designation' => $designation,
                    'join_date' => $join,
                    'contract_month' => $duration,
                    'end_date' => $endDate,
                    'project_name' => $projectId,
                    'salary' => is_numeric($salary) ? (int)$salary : null,
                    'location' => $location,
                    'organization' => $organization,
                    'status' => 'active',
                ];
                $this->shrm->insert('contract_details', $contractData);

                $imported++;

            } catch (Exception $e) {
                // Log error with complete row data
                fwrite($fpLog, "\n[ERROR] Exception occurred at row " . ($rowIndex + 2) . ": " . $e->getMessage() . "\n");
                fwrite($fpLog, "[ERROR] Complete Excel row data:\n");
                fwrite($fpLog, "  EmpID: {$empId}\n");
                fwrite($fpLog, "  FullName: {$fullName}\n");
                fwrite($fpLog, "  PersonalEmail: {$personalEmail}\n");
                fwrite($fpLog, "  DOB: {$dobRaw}\n");
                fwrite($fpLog, "  Gender: {$gender}\n");
                fwrite($fpLog, "  Mobile: {$mobile}\n");
                fwrite($fpLog, "  PAN: {$pan}\n");
                fwrite($fpLog, "  Address: {$address}\n");
                fwrite($fpLog, "  ProfEmail: {$profEmail}\n");
                fwrite($fpLog, "  Stream: {$stream}\n");
                fwrite($fpLog, "  ReportingOfficer: {$reportingOfficerName}\n");
                fwrite($fpLog, "  Designation: {$designation}\n");
                fwrite($fpLog, "  JoinDate: {$joinRaw}\n");
                fwrite($fpLog, "  Duration: {$duration}\n");
                fwrite($fpLog, "  Salary: {$salary}\n");
                fwrite($fpLog, "  Project: {$project}\n");
                fwrite($fpLog, "  Location: {$location}\n");
                fwrite($fpLog, "  Organization: {$organization}\n");
                fwrite($fpLog, "  Raw Row Data: " . json_encode($row) . "\n\n");

                $errors++;
                // Continue processing other rows instead of stopping
                continue;
            }
        }

        // complete transaction
        $this->shrm->trans_complete();
        fclose($fpLog);

        // summary
        echo "<p>Imported {$imported} new users. Skipped {$skipped} existing. Errors: {$errors}</p>";
        echo "<p>See log: <code>uploads/" . basename($logPath) . "</code></p>";
    }

    /**
     * Parse and format dates, handle Excel serial numbers and strings
     */
    private function _formatDate($value)
    {
        // Excel stores dates as serial numbers
        if (is_numeric($value)) {
            // Convert Excel date to UNIX timestamp
            $ts = ($value - 25569) * 86400;
            return gmdate('Y-m-d', (int)$ts);
        }
        // Fallback to strtotime for strings
        $ts = strtotime($value);
        return $ts ? date('Y-m-d', $ts) : null;
    }
}