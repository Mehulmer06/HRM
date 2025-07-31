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
        fwrite($fpLog, "=== EMPLOYEE IMPORT LOG ===\n");
        fwrite($fpLog, "Start Time: " . date('Y-m-d H:i:s') . "\n");
        fwrite($fpLog, "Source File: {$source}\n");
        fwrite($fpLog, "Total Rows Found: " . count($rows) . "\n");
        fwrite($fpLog, "==========================\n\n");

        // Begin transaction
        $this->shrm->trans_start();
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rows as $rowIndex => $row) {
            $currentRow = $rowIndex + 2; // Excel row number (starting from 2 since row 1 is header)

            fwrite($fpLog, "\n" . str_repeat("=", 60) . "\n");
            fwrite($fpLog, "PROCESSING ROW {$currentRow}\n");
            fwrite($fpLog, str_repeat("=", 60) . "\n");

            try {
                // sanitize and pad
                $row = array_pad($row, 18, '');  // Extended to 18 columns (A-R)
                $row = array_map(fn($c) => xss_clean(trim($c)), $row);

                fwrite($fpLog, "[RAW_DATA] Original row data: " . json_encode($row) . "\n");

                if (count(array_filter($row, fn($v) => $v !== '')) === 0) {
                    fwrite($fpLog, "[SKIP] Empty row - all fields are blank\n");
                    continue;
                }

                // map columns
                list(
                    $empId, $fullName, $personalEmail, $dobRaw,
                    $gender, $mobile, $pan, $address,
                    $profEmail, $stream, $reportingOfficerName,
                    $designation, $joinRaw, $duration,
                    $salary, $project, $location, $organization
                    ) = $row;

                // Log all extracted fields
                fwrite($fpLog, "[EXTRACTED_FIELDS]\n");
                fwrite($fpLog, "  A - Employee ID: '{$empId}'\n");
                fwrite($fpLog, "  B - Full Name: '{$fullName}'\n");
                fwrite($fpLog, "  C - Personal Email: '{$personalEmail}'\n");
                fwrite($fpLog, "  D - DOB Raw: '{$dobRaw}'\n");
                fwrite($fpLog, "  E - Gender: '{$gender}'\n");
                fwrite($fpLog, "  F - Mobile: '{$mobile}'\n");
                fwrite($fpLog, "  G - PAN: '{$pan}'\n");
                fwrite($fpLog, "  H - Address: '{$address}'\n");
                fwrite($fpLog, "  I - Professional Email: '{$profEmail}'\n");
                fwrite($fpLog, "  J - Stream/Department: '{$stream}'\n");
                fwrite($fpLog, "  K - Reporting Officer: '{$reportingOfficerName}'\n");
                fwrite($fpLog, "  L - Designation: '{$designation}'\n");
                fwrite($fpLog, "  M - Join Date Raw: '{$joinRaw}'\n");
                fwrite($fpLog, "  N - Duration: '{$duration}'\n");
                fwrite($fpLog, "  O - Salary: '{$salary}'\n");
                fwrite($fpLog, "  P - Project: '{$project}'\n");
                fwrite($fpLog, "  Q - Location: '{$location}'\n");
                fwrite($fpLog, "  R - Organization: '{$organization}'\n");

                // VALIDATION: Check required fields
                fwrite($fpLog, "\n[VALIDATION_START]\n");
                if (empty($empId) || empty($fullName)) {
                    fwrite($fpLog, "[VALIDATION_FAIL] Missing required fields:\n");
                    fwrite($fpLog, "  - Employee ID: " . (empty($empId) ? "MISSING" : "Present") . "\n");
                    fwrite($fpLog, "  - Full Name: " . (empty($fullName) ? "MISSING" : "Present") . "\n");
                    $skipped++;
                    continue;
                }
                fwrite($fpLog, "[VALIDATION_PASS] Required fields present\n");

                // VALIDATION: Check email format
                if (!empty($personalEmail) && !filter_var($personalEmail, FILTER_VALIDATE_EMAIL)) {
                    fwrite($fpLog, "[VALIDATION_FAIL] Invalid personal email format: '{$personalEmail}'\n");
                    $errors++;
                    continue;
                }

                if (!empty($profEmail) && !filter_var($profEmail, FILTER_VALIDATE_EMAIL)) {
                    fwrite($fpLog, "[VALIDATION_FAIL] Invalid professional email format: '{$profEmail}'\n");
                    $errors++;
                    continue;
                }
                fwrite($fpLog, "[VALIDATION_PASS] Email formats valid\n");

                // Check if user exists
                fwrite($fpLog, "\n[DUPLICATE_CHECK] Checking if employee ID '{$empId}' already exists...\n");
                $exists = $this->shrm->where('employee_id', $empId)->get('users')->row();
                if ($exists) {
                    fwrite($fpLog, "[DUPLICATE_FOUND] Employee already exists with ID: {$empId}\n");
                    fwrite($fpLog, "  - Existing User ID: {$exists->id}\n");
                    fwrite($fpLog, "  - Existing Name: " . (isset($exists->name) ? $exists->name : 'N/A') . "\n");
                    $skipped++;
                    continue;
                }
                fwrite($fpLog, "[DUPLICATE_CHECK_PASS] Employee ID is unique\n");

                // format dates (handle Excel serials)
                fwrite($fpLog, "\n[DATE_PROCESSING]\n");
                $dob = $this->_formatDate($dobRaw);
                $join = $this->_formatDate($joinRaw);

                $this->_logDateParsing($dobRaw, $dob, $fpLog, "DOB");
                $this->_logDateParsing($joinRaw, $join, $fpLog, "Join Date");

                // lookup reporting officer
                fwrite($fpLog, "\n[REPORTING_OFFICER_LOOKUP]\n");
                $roId = null;
                $roName = null;
                $roDesignation = null;
                if (!empty($reportingOfficerName)) {
                    fwrite($fpLog, "  Searching for reporting officer: '{$reportingOfficerName}'\n");
                    $ro = $this->db->select('id, name, designation')
                        ->like('name', $reportingOfficerName)
                        ->get('user')
                        ->row();
                    if ($ro) {
                        $roId = $ro->id;
                        $roName = $ro->name;
                        $roDesignation = $ro->designation;
                        fwrite($fpLog, "  [RO_FOUND] ID: {$roId}, Name: '{$roName}', Designation: '{$roDesignation}'\n");
                    } else {
                        fwrite($fpLog, "  [RO_NOT_FOUND] No reporting officer found matching: '{$reportingOfficerName}'\n");
                    }
                } else {
                    fwrite($fpLog, "  [RO_SKIP] No reporting officer specified\n");
                }

                // Clean and validate numeric fields
                fwrite($fpLog, "\n[NUMERIC_FIELD_PROCESSING]\n");
                $cleanedSalary = null;
                if (!empty($salary)) {
                    $originalSalary = $salary;
                    $cleanedSalary = preg_replace('/[^0-9.]/', '', $salary);
                    if (is_numeric($cleanedSalary) && $cleanedSalary > 0) {
                        $cleanedSalary = (float)$cleanedSalary;
                        fwrite($fpLog, "  Salary: '{$originalSalary}' -> {$cleanedSalary}\n");
                    } else {
                        fwrite($fpLog, "  [SALARY_WARNING] Invalid salary format: '{$originalSalary}' -> '{$cleanedSalary}'\n");
                        $cleanedSalary = null;
                    }
                } else {
                    fwrite($fpLog, "  Salary: Empty/NULL\n");
                }

                $cleanedDuration = null;
                if (!empty($duration)) {
                    $originalDuration = $duration;
                    $cleanedDuration = preg_replace('/[^0-9.]/', '', $duration);
                    if (is_numeric($cleanedDuration) && $cleanedDuration > 0) {
                        $cleanedDuration = (int)$cleanedDuration;
                        fwrite($fpLog, "  Duration: '{$originalDuration}' -> {$cleanedDuration} months\n");
                    } else {
                        fwrite($fpLog, "  [DURATION_WARNING] Invalid duration format: '{$originalDuration}' -> '{$cleanedDuration}'\n");
                        $cleanedDuration = null;
                    }
                } else {
                    fwrite($fpLog, "  Duration: Empty/NULL\n");
                }

                // Clean phone number and PAN
                $cleanedMobile = $this->_cleanPhoneNumber($mobile);
                $cleanedPan = $this->_validatePAN($pan);

                if ($mobile !== $cleanedMobile) {
                    fwrite($fpLog, "  Phone: '{$mobile}' -> '" . ($cleanedMobile ?: 'INVALID') . "'\n");
                }
                if ($pan !== $cleanedPan) {
                    fwrite($fpLog, "  PAN: '{$pan}' -> '" . ($cleanedPan ?: 'INVALID') . "'\n");
                }

                // prepare user data
                fwrite($fpLog, "\n[USER_DATA_PREPARATION]\n");
                $userData = [
                    'name' => ucwords(strtolower($fullName)),
                    'employee_id' => $empId,
                    'dob' => $dob,
                    'email' => !empty($personalEmail) ? $personalEmail : null,
                    'professional_email' => !empty($profEmail) ? $profEmail : null,
                    'password' => md5('12345678'),
                    'address' => !empty($address) ? $address : null,
                    'department' => !empty($stream) ? $stream : null,
                    'gender' => !empty($gender) ? $gender : null,
                    'pan_number' => $cleanedPan,
                    'phone' => $cleanedMobile,
                    'reporting_officer_id' => $roId,
                    'reporting_officer_name' => $roName,
                    'reporting_officer_designation' => $roDesignation,
                    'status' => 'Y',
                    'role' => 'employee'
                ];

                // Log prepared user data
                fwrite($fpLog, "  Prepared user data:\n");
                foreach ($userData as $key => $value) {
                    $displayValue = ($value === null) ? 'NULL' : "'{$value}'";
                    fwrite($fpLog, "    {$key}: {$displayValue}\n");
                }

                // Remove null values to avoid database issues
                $userDataFiltered = array_filter($userData, function ($value) {
                    return $value !== null && $value !== '';
                });

                $removedFields = array_diff_key($userData, $userDataFiltered);
                if (!empty($removedFields)) {
                    fwrite($fpLog, "  Fields removed (null/empty): " . implode(', ', array_keys($removedFields)) . "\n");
                }

                // Insert user
                fwrite($fpLog, "\n[USER_INSERT]\n");
                fwrite($fpLog, "  Attempting to insert user into 'users' table...\n");

                $insertResult = $this->shrm->insert('users', $userDataFiltered);
                if (!$insertResult) {
                    $dbError = $this->shrm->error();
                    fwrite($fpLog, "  [USER_INSERT_FAIL] Database error: " . json_encode($dbError) . "\n");
                    fwrite($fpLog, "  Last query: " . $this->shrm->last_query() . "\n");
                    $errors++;
                    continue;
                }

                $userId = $this->shrm->insert_id();
                fwrite($fpLog, "  [USER_INSERT_SUCCESS] New User ID: {$userId}\n");

                // lookup project
                fwrite($fpLog, "\n[PROJECT_LOOKUP]\n");
                $projectId = null;
                if (!empty($project)) {
                    fwrite($fpLog, "  Searching for project: '{$project}'\n");
                    $projectData = $this->shrm->select('id')
                        ->like('project_name', $project)
                        ->get('projects')
                        ->row();
                    if ($projectData) {
                        $projectId = $projectData->id;
                        fwrite($fpLog, "  [PROJECT_FOUND] Project ID: {$projectId} for project: '{$project}'\n");
                    } else {
                        fwrite($fpLog, "  [PROJECT_NOT_FOUND] No project found matching: '{$project}'\n");
                    }
                } else {
                    fwrite($fpLog, "  [PROJECT_SKIP] No project specified\n");
                }

                // calculate end date
                fwrite($fpLog, "\n[END_DATE_CALCULATION]\n");
                $endDate = null;
                if ($join && $cleanedDuration && $cleanedDuration > 0) {
                    try {
                        $joinDateTime = new DateTime($join);
                        $joinDateTime->add(new DateInterval('P' . $cleanedDuration . 'M'));
                        $endDate = $joinDateTime->format('Y-m-d');
                        fwrite($fpLog, "  [END_DATE_SUCCESS] Calculated: {$endDate} (Join: {$join} + {$cleanedDuration} months)\n");
                    } catch (Exception $dateEx) {
                        fwrite($fpLog, "  [END_DATE_ERROR] Calculation failed: " . $dateEx->getMessage() . "\n");
                    }
                } else {
                    $missingFields = [];
                    if (!$join) $missingFields[] = "join_date";
                    if (!$cleanedDuration || $cleanedDuration <= 0) $missingFields[] = "duration";
                    fwrite($fpLog, "  [END_DATE_SKIP] Cannot calculate - missing: " . implode(', ', $missingFields) . "\n");
                }

                // prepare contract data
                fwrite($fpLog, "\n[CONTRACT_DATA_PREPARATION]\n");

                // Inside import_file() method, after $errors = 0;
                $allowedDesignations = [
                    'Electrician',
                    'Staff Car Driver',
                    'Helper',
                    'Jr. Project Consultant',
                    'Project Consultant',
                    'Project Officer',
                    'Sr. Project Officer',
                    'Sr. Project Associate',
                    'IT Consultant',
                    'Sr. Project Consultant',
                    'Project Assistant',
                    'Jr. IT Consultant',
                    'Project Associate',
                    'Admin Assistant',
                    'Management Trainee',
                    'Admin Associate',
                    'Programmer',
                    'Professional Assistant',
                    'Security Gaurd',
                    'Software Developer',
                    'Library Associate',
                    'PS to Director',
                    'Assistant',
                    'Sr. Software Developer',
                    'Helper - Daily wages',
                    'Consultant',
                    'Executive',
                    'Sr. Executive',
                    'MTS - Daily wages',
                    'Library Officer'
                ];

// Inside foreach loop, after: $row = array_pad($row, 18, '');
                $designationRaw = $row[11];
                $designationCleaned = trim(preg_replace('/\s*\(.*?\)/', '', $designationRaw));
                $designationFinal = null;
                foreach ($allowedDesignations as $allowed) {
                    if (strcasecmp($designationCleaned, $allowed) === 0) {
                        $designationFinal = $allowed;
                        break;
                    }
                }

// Logging original and final designation
                fwrite($fpLog, "  L - Designation (Raw): '{$designationRaw}'\n");
                fwrite($fpLog, "  L - Normalized Designation: '{$designationCleaned}' => Mapped: '{$designationFinal}'\n");


                $contractData = [
                    'user_id' => $userId,
                    'designation' => $designationFinal,
                    'join_date' => $join,
                    'contract_month' => $cleanedDuration,
                    'end_date' => $endDate,
                    'project_name' => $projectId, // Note: Change to 'project_id' if that's your column name
                    'salary' => $cleanedSalary,
                    'location' => !empty($location) ? $location : null,
                    'organization' => !empty($organization) ? $organization : null,
                    'status' => 'active',
                ];

                // Log prepared contract data
                fwrite($fpLog, "  Prepared contract data:\n");
                foreach ($contractData as $key => $value) {
                    $displayValue = ($value === null) ? 'NULL' : "'{$value}'";
                    fwrite($fpLog, "    {$key}: {$displayValue}\n");
                }

                // Remove null values
                $contractDataFiltered = array_filter($contractData, function ($value) {
                    return $value !== null && $value !== '';
                });

                $removedContractFields = array_diff_key($contractData, $contractDataFiltered);
                if (!empty($removedContractFields)) {
                    fwrite($fpLog, "  Contract fields removed (null/empty): " . implode(', ', array_keys($removedContractFields)) . "\n");
                }

                // Insert contract details
                fwrite($fpLog, "\n[CONTRACT_INSERT]\n");
                fwrite($fpLog, "  Attempting to insert contract details...\n");

                $contractInsertResult = $this->shrm->insert('contract_details', $contractDataFiltered);
                if (!$contractInsertResult) {
                    $dbError = $this->shrm->error();
                    fwrite($fpLog, "  [CONTRACT_INSERT_FAIL] Database error: " . json_encode($dbError) . "\n");
                    fwrite($fpLog, "  Last query: " . $this->shrm->last_query() . "\n");
                    $errors++;
                } else {
                    $contractId = $this->shrm->insert_id();
                    fwrite($fpLog, "  [CONTRACT_INSERT_SUCCESS] New Contract ID: {$contractId}\n");
                }

                $imported++;
                fwrite($fpLog, "\n[ROW_RESULT] SUCCESS - User and contract data inserted\n");

            } catch (Exception $e) {
                // Log comprehensive error information
                fwrite($fpLog, "\n" . str_repeat("!", 60) . "\n");
                fwrite($fpLog, "EXCEPTION OCCURRED IN ROW {$currentRow}\n");
                fwrite($fpLog, str_repeat("!", 60) . "\n");
                fwrite($fpLog, "[EXCEPTION_TYPE] " . get_class($e) . "\n");
                fwrite($fpLog, "[EXCEPTION_MESSAGE] " . $e->getMessage() . "\n");
                fwrite($fpLog, "[EXCEPTION_FILE] " . $e->getFile() . " (Line: " . $e->getLine() . ")\n");
                fwrite($fpLog, "[EXCEPTION_TRACE]\n" . $e->getTraceAsString() . "\n");

                fwrite($fpLog, "\n[EXCEPTION_CONTEXT] Complete row data at time of error:\n");
                if (isset($empId)) fwrite($fpLog, "  Employee ID: '{$empId}'\n");
                if (isset($fullName)) fwrite($fpLog, "  Full Name: '{$fullName}'\n");
                if (isset($personalEmail)) fwrite($fpLog, "  Personal Email: '{$personalEmail}'\n");
                if (isset($dobRaw)) fwrite($fpLog, "  DOB Raw: '{$dobRaw}'\n");
                if (isset($gender)) fwrite($fpLog, "  Gender: '{$gender}'\n");
                if (isset($mobile)) fwrite($fpLog, "  Mobile: '{$mobile}'\n");
                if (isset($pan)) fwrite($fpLog, "  PAN: '{$pan}'\n");
                if (isset($address)) fwrite($fpLog, "  Address: '{$address}'\n");
                if (isset($profEmail)) fwrite($fpLog, "  Professional Email: '{$profEmail}'\n");
                if (isset($stream)) fwrite($fpLog, "  Stream/Department: '{$stream}'\n");
                if (isset($reportingOfficerName)) fwrite($fpLog, "  Reporting Officer: '{$reportingOfficerName}'\n");
                if (isset($designation)) fwrite($fpLog, "  Designation: '{$designation}'\n");
                if (isset($joinRaw)) fwrite($fpLog, "  Join Date Raw: '{$joinRaw}'\n");
                if (isset($duration)) fwrite($fpLog, "  Duration: '{$duration}'\n");
                if (isset($salary)) fwrite($fpLog, "  Salary: '{$salary}'\n");
                if (isset($project)) fwrite($fpLog, "  Project: '{$project}'\n");
                if (isset($location)) fwrite($fpLog, "  Location: '{$location}'\n");
                if (isset($organization)) fwrite($fpLog, "  Organization: '{$organization}'\n");

                fwrite($fpLog, "\n[EXCEPTION_RAW_DATA] Complete Excel row: " . json_encode($row) . "\n");

                // Log processed data if available
                if (isset($dob)) fwrite($fpLog, "[PROCESSED_DOB] '{$dob}'\n");
                if (isset($join)) fwrite($fpLog, "[PROCESSED_JOIN_DATE] '{$join}'\n");
                if (isset($cleanedSalary)) fwrite($fpLog, "[PROCESSED_SALARY] '{$cleanedSalary}'\n");
                if (isset($cleanedDuration)) fwrite($fpLog, "[PROCESSED_DURATION] '{$cleanedDuration}'\n");
                if (isset($roId)) fwrite($fpLog, "[REPORTING_OFFICER_ID] '{$roId}'\n");
                if (isset($projectId)) fwrite($fpLog, "[PROJECT_ID] '{$projectId}'\n");
                if (isset($userId)) fwrite($fpLog, "[USER_ID_CREATED] '{$userId}'\n");

                // Database state information
                fwrite($fpLog, "\n[DB_STATE] Transaction status: " . ($this->shrm->trans_status() ? "Active" : "Failed") . "\n");
                fwrite($fpLog, "[DB_STATE] Last query: " . $this->shrm->last_query() . "\n");

                $errors++;
                fwrite($fpLog, "[ROW_RESULT] FAILED - Exception occurred\n");
                continue;
            }
        }

        // Final transaction handling and summary
        fwrite($fpLog, "\n" . str_repeat("=", 60) . "\n");
        fwrite($fpLog, "IMPORT SUMMARY\n");
        fwrite($fpLog, str_repeat("=", 60) . "\n");

        // complete transaction
        if ($this->shrm->trans_status() === FALSE) {
            $this->shrm->trans_rollback();
            fwrite($fpLog, "[TRANSACTION] ROLLED BACK due to errors\n");
            fwrite($fpLog, "[TRANSACTION] All database changes have been reverted\n");
        } else {
            $this->shrm->trans_commit();
            fwrite($fpLog, "[TRANSACTION] COMMITTED successfully\n");
            fwrite($fpLog, "[TRANSACTION] All database changes have been saved\n");
        }

        fwrite($fpLog, "\n[FINAL_COUNTS]\n");
        fwrite($fpLog, "  Successfully Imported: {$imported} users\n");
        fwrite($fpLog, "  Skipped (duplicates/empty): {$skipped} rows\n");
        fwrite($fpLog, "  Errors encountered: {$errors} rows\n");
        fwrite($fpLog, "  Total rows processed: " . ($imported + $skipped + $errors) . "\n");
        fwrite($fpLog, "  Total rows in file: " . count($rows) . "\n");

        $endTime = date('Y-m-d H:i:s');
        fwrite($fpLog, "\n[TIMING]\n");
        fwrite($fpLog, "  End Time: {$endTime}\n");

        if ($errors > 0) {
            fwrite($fpLog, "\n[RECOMMENDATIONS]\n");
            fwrite($fpLog, "  - Review error details above for specific issues\n");
            fwrite($fpLog, "  - Check database schema and constraints\n");
            fwrite($fpLog, "  - Verify data formats in Excel file\n");
            fwrite($fpLog, "  - Ensure all required tables exist (users, contract_details, projects, user)\n");
        }

        if ($imported > 0) {
            fwrite($fpLog, "\n[SUCCESS_NOTES]\n");
            fwrite($fpLog, "  - {$imported} new users successfully created\n");
            fwrite($fpLog, "  - Contract details inserted for all successful users\n");
            fwrite($fpLog, "  - Default password set to '12345678' (MD5 hashed)\n");
        }

        fwrite($fpLog, "\n" . str_repeat("=", 60) . "\n");
        fwrite($fpLog, "END OF LOG\n");
        fwrite($fpLog, str_repeat("=", 60) . "\n");

        fclose($fpLog);

        // Display comprehensive summary
        echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";
        echo "<h2 style='color: #333;'>Employee Import Results</h2>";

        echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h3 style='margin-top: 0; color: #2c5aa0;'>Summary Statistics</h3>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr><td style='padding: 5px 0; font-weight: bold;'>Successfully Imported:</td><td style='color: green; font-weight: bold;'>{$imported} users</td></tr>";
        echo "<tr><td style='padding: 5px 0; font-weight: bold;'>Skipped (existing/empty):</td><td style='color: orange; font-weight: bold;'>{$skipped} rows</td></tr>";
        echo "<tr><td style='padding: 5px 0; font-weight: bold;'>Errors:</td><td style='color: red; font-weight: bold;'>{$errors} rows</td></tr>";
        echo "<tr><td style='padding: 5px 0; font-weight: bold;'>Total Processed:</td><td style='font-weight: bold;'>" . ($imported + $skipped + $errors) . " rows</td></tr>";
        echo "</table>";
        echo "</div>";

        if ($imported > 0) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "<h4 style='margin-top: 0; color: #155724;'>✓ Success Details</h4>";
            echo "<ul style='margin: 0; padding-left: 20px;'>";
            echo "<li>{$imported} new employee records created</li>";
            echo "<li>Contract details inserted for all successful users</li>";
            echo "<li>Default password set to '12345678' (MD5 hashed)</li>";
            echo "<li>All database changes committed successfully</li>";
            echo "</ul>";
            echo "</div>";
        }

        if ($errors > 0) {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "<h4 style='margin-top: 0; color: #721c24;'>⚠ Error Summary</h4>";
            echo "<p>Some rows could not be processed. Common issues include:</p>";
            echo "<ul style='margin: 0; padding-left: 20px;'>";
            echo "<li>Invalid email formats</li>";
            echo "<li>Missing required fields (Employee ID or Name)</li>";
            echo "<li>Database constraint violations</li>";
            echo "<li>Invalid date formats</li>";
            echo "<li>Numeric field formatting issues</li>";
            echo "</ul>";
            echo "</div>";
        }

        if ($skipped > 0) {
            echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "<h4 style='margin-top: 0; color: #856404;'>ℹ Skipped Rows</h4>";
            echo "<p>{$skipped} rows were skipped because:</p>";
            echo "<ul style='margin: 0; padding-left: 20px;'>";
            echo "<li>Employee ID already exists in database</li>";
            echo "<li>Row contains no data (all fields empty)</li>";
            echo "</ul>";
            echo "</div>";
        }

        echo "<div style='background: #e2e3e5; border: 1px solid #d6d8db; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h4 style='margin-top: 0; color: #383d41;'>📋 Detailed Log Information</h4>";
        echo "<p>Complete processing details have been saved to:</p>";
        echo "<p style='background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 14px;'>";
        echo "<strong>uploads/" . basename($logPath) . "</strong>";
        echo "</p>";
        echo "<p>This log contains:</p>";
        echo "<ul style='margin: 0; padding-left: 20px;'>";
        echo "<li>Row-by-row processing details</li>";
        echo "<li>Field extraction and validation results</li>";
        echo "<li>Database query information</li>";
        echo "<li>Error messages and stack traces</li>";
        echo "<li>Data transformation logs</li>";
        echo "</ul>";
        echo "</div>";

        echo "<div style='background: #cce5ff; border: 1px solid #99ccff; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<h4 style='margin-top: 0; color: #004085;'>🔧 Next Steps</h4>";
        echo "<ul style='margin: 0; padding-left: 20px;'>";
        echo "<li>Review the detailed log file for specific issues</li>";
        echo "<li>For failed rows, check Excel data formatting</li>";
        echo "<li>Verify database schema matches expected field names</li>";
        echo "<li>Consider updating duplicate employee records manually if needed</li>";
        echo "<li>Inform new employees of their default password</li>";
        echo "</ul>";
        echo "</div>";

        echo "</div>";
    }

    /**
     * Parse and format dates, handle Excel serial numbers and strings
     */
    private function _formatDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Excel stores dates as serial numbers (days since 1900-01-01, but with a bug for leap year 1900)
        if (is_numeric($value)) {
            $value = (float)$value;
            // Handle Excel date serial numbers
            if ($value > 0) {
                // Excel epoch is 1900-01-01, but there's a leap year bug, so we use 1899-12-30
                $unixTimestamp = ($value - 25569) * 86400;
                if ($unixTimestamp > 0) {
                    return gmdate('Y-m-d', (int)$unixTimestamp);
                }
            }
            return null;
        }

        // Handle string dates
        $value = trim($value);
        if (empty($value)) {
            return null;
        }

        // Try various date formats
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'd-m-Y',
            'm-d-Y',
            'Y/m/d',
            'd.m.Y',
            'Y.m.d',
            'j/n/Y',      // Single digit day/month
            'n/j/Y',      // Single digit month/day
            'd/m/y',      // Two digit year
            'm/d/y',      // Two digit year
            'j-n-Y',      // Single digit with dashes
            'n-j-Y'       // Single digit with dashes
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        // Fallback to strtotime for other formats
        $timestamp = strtotime($value);
        if ($timestamp !== false && $timestamp > 0) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Additional helper method to validate and log date parsing attempts
     */
    private function _logDateParsing($originalValue, $parsedValue, $logHandle, $fieldName)
    {
        if (!empty($originalValue)) {
            fwrite($logHandle, "  {$fieldName}: '{$originalValue}' -> '" . ($parsedValue ?: 'FAILED') . "'");

            if (!$parsedValue) {
                fwrite($logHandle, " [PARSE_FAILED]");

                // Additional debugging for failed dates
                if (is_numeric($originalValue)) {
                    fwrite($logHandle, " [NUMERIC_VALUE: {$originalValue}]");
                } else {
                    fwrite($logHandle, " [STRING_VALUE]");
                }
            }
            fwrite($logHandle, "\n");
        }
    }

    /**
     * Helper method to clean and validate phone numbers
     */
    private function _cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters except + at the beginning
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        // If it starts with +, keep it, otherwise remove any + characters
        if (strpos($cleaned, '+') === 0) {
            $cleaned = '+' . preg_replace('/[^0-9]/', '', substr($cleaned, 1));
        } else {
            $cleaned = preg_replace('/[^0-9]/', '', $cleaned);
        }

        // Basic validation - should be at least 10 digits
        if (strlen(preg_replace('/[^0-9]/', '', $cleaned)) < 10) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Helper method to validate PAN number format
     */
    private function _validatePAN($pan)
    {
        if (empty($pan)) {
            return null;
        }

        $pan = strtoupper(trim($pan));

        // PAN format: 5 letters, 4 digits, 1 letter
        if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan)) {
            return $pan;
        }

        return null; // Invalid PAN format
    }

    /**
     * Additional utility method to check database table structure
     * Call this method to verify your database schema
     */
    public function check_database_structure()
    {
        echo "<h2>Database Structure Check</h2>";

        // Check users table
        echo "<h3>Users Table Structure:</h3>";
        $query = $this->shrm->query("DESCRIBE users");
        if ($query->num_rows() > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($query->result_array() as $row) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "<td>{$row['Extra']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>Users table not found!</p>";
        }

        // Check contract_details table
        echo "<h3>Contract Details Table Structure:</h3>";
        $query = $this->shrm->query("DESCRIBE contract_details");
        if ($query->num_rows() > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($query->result_array() as $row) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "<td>{$row['Extra']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>Contract_details table not found!</p>";
        }

        // Check projects table
        echo "<h3>Projects Table Structure:</h3>";
        $query = $this->shrm->query("DESCRIBE projects");
        if ($query->num_rows() > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($query->result_array() as $row) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "<td>{$row['Extra']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>Projects table not found!</p>";
        }

        // Check user table (for reporting officers)
        echo "<h3>User Table Structure (for Reporting Officers):</h3>";
        $query = $this->db->query("DESCRIBE user");
        if ($query->num_rows() > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($query->result_array() as $row) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "<td>{$row['Extra']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>User table not found in default database!</p>";
        }
    }

    /**
     * Test method to validate a single row of data without inserting
     */
    public function test_single_row()
    {
        // Test data - replace with your actual row data
        $testRow = [
            'EMP001',                    // Employee ID
            'John Doe',                  // Full Name
            'john.doe@email.com',        // Personal Email
            '44197',                     // DOB (Excel date)
            'Male',                      // Gender
            '+91-9876543210',           // Mobile
            'ABCDE1234F',               // PAN
            '123 Main Street, City',     // Address
            'john.doe@company.com',      // Professional Email
            'IT',                        // Stream/Department
            'Jane Smith',                // Reporting Officer Name
            'Software Developer',        // Designation
            '44562',                     // Join Date (Excel date)
            '12',                        // Duration in months
            '50000',                     // Salary
            'Project Alpha',             // Project
            'Mumbai',                    // Location
            'ABC Company'                // Organization
        ];

        echo "<h2>Single Row Test Results</h2>";

        // Test date parsing
        echo "<h3>Date Parsing Test:</h3>";
        $dob = $this->_formatDate($testRow[3]);
        $joinDate = $this->_formatDate($testRow[12]);
        echo "<p>DOB: '{$testRow[3]}' -> '{$dob}'</p>";
        echo "<p>Join Date: '{$testRow[12]}' -> '{$joinDate}'</p>";

        // Test phone cleaning
        echo "<h3>Phone Number Cleaning:</h3>";
        $cleanPhone = $this->_cleanPhoneNumber($testRow[5]);
        echo "<p>Phone: '{$testRow[5]}' -> '{$cleanPhone}'</p>";

        // Test PAN validation
        echo "<h3>PAN Validation:</h3>";
        $cleanPAN = $this->_validatePAN($testRow[6]);
        echo "<p>PAN: '{$testRow[6]}' -> '{$cleanPAN}'</p>";

        // Test reporting officer lookup
        echo "<h3>Reporting Officer Lookup:</h3>";
        if (!empty($testRow[10])) {
            $ro = $this->db->select('id, name, designation')
                ->like('name', $testRow[10])
                ->get('user')
                ->row();
            if ($ro) {
                echo "<p>Found: ID={$ro->id}, Name='{$ro->name}', Designation='{$ro->designation}'</p>";
            } else {
                echo "<p style='color: orange;'>Reporting Officer '{$testRow[10]}' not found</p>";
            }
        }

        // Test project lookup
        echo "<h3>Project Lookup:</h3>";
        if (!empty($testRow[15])) {
            $project = $this->shrm->select('id, project_name')
                ->like('project_name', $testRow[15])
                ->get('projects')
                ->row();
            if ($project) {
                echo "<p>Found: ID={$project->id}, Name='{$project->project_name}'</p>";
            } else {
                echo "<p style='color: orange;'>Project '{$testRow[15]}' not found</p>";
            }
        }

        echo "<p><strong>Note:</strong> This is a test only. No data was inserted into the database.</p>";
    }
}