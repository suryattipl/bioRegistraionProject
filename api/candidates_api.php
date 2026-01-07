<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config.php';
session_start();
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
header('Content-Type: application/json');
error_reporting(0);

// $action = $_GET['action'] ?? '';
$action = $_REQUEST['action'] ?? '';

switch ($action) {

case 'getProjectDetails':

    header('Content-Type: application/json');

    if (empty($_POST['project_id'])) {
        echo json_encode(["success" => false]);
        exit;
    }

    $project_id = $_POST['project_id'];

    $stmt = $con1->prepare("
        SELECT morning_shift, evening_shift, same_candidates_in_shifts
        FROM projects
        WHERE id = ?
    ");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "success" => true,
            "data" => $row
        ]);
    } else {
        echo json_encode(["success" => false]);
    }

break;



/* case 'uploadExcel':

    if (
        empty($_POST['project_id']) ||
        !isset($_FILES['excel_file']) ||
        $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Project ID or Excel file missing'
        ]);
        exit;
    }

    $project_id = (int) $_POST['project_id'];

    // ✅ Dynamic table name
    $candidateTable = $project_id . "_candidate_details";

    // ✅ Load Excel
    require_once __DIR__ . '/../vendor/autoload.php';

    $file = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Excel file'
        ]);
        exit;
    }

    if (count($rows) < 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Excel file is empty'
        ]);
        exit;
    }

    // ✅ Excel headers + data
    $excel_headers = array_map('trim', $rows[0]);
    $excel_data    = array_slice($rows, 1);

    // ✅ Store preview data in session
    $_SESSION['excel_headers'] = $excel_headers;
    $_SESSION['excel_data']    = $excel_data;
    $_SESSION['project_id']    = $project_id;

    // ✅ Fetch DB columns dynamically from project table
    $db_columns = [];

    $colRes = mysqli_query($con1, "SHOW COLUMNS FROM `$candidateTable`");

    if (!$colRes) {
        echo json_encode([
            'success' => false,
            'message' => 'Candidate table not found for project'
        ]);
        exit;
    }

    while ($col = mysqli_fetch_assoc($colRes)) {
        if (!in_array($col['Field'], ['id', 'created_at', 'updated_at'])) {
            $db_columns[] = $col['Field'];
        }
    }

    echo json_encode([
        'success'      => true,
        'project_id'   => $project_id,
        'headers'      => $excel_headers,
        'db_columns'   => $db_columns,
        'preview_rows' => array_slice($excel_data, 0, 5) // preview first 5 rows
    ]);

break; */
case 'uploadExcel':

    if (
        empty($_POST['project_id']) ||
        !isset($_FILES['excel_file']) ||
        $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK
    ) {
        echo json_encode([
            'success' => false,
            'message' => 'Project ID or Excel file missing'
        ]);
        exit;
    }

    $project_id = (int) $_POST['project_id'];
    $candidateTable = $project_id . "_candidate_details";
    try {
    // ✅ debug safely
    error_log('Loading excel file');

    $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load(
        $_FILES['excel_file']['tmp_name']
    );

    $rows = $spreadsheet->getActiveSheet()->toArray();


} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Invalid Excel file',
        'error'   => $e->getMessage() // TEMP for debug
    ]);
    exit;
}

if (count($rows) < 2) {
    echo json_encode([
        'success' => false,
        'message' => 'Excel file is empty'
    ]);
    exit;
}

$excel_headers = array_map('trim', $rows[0]);
$excel_data    = array_slice($rows, 1);

$_SESSION['excel_headers'] = $excel_headers;
$_SESSION['excel_data']    = $excel_data;
$_SESSION['project_id']    = $project_id;

$db_columns = [];
$colRes = mysqli_query($con1, "SHOW COLUMNS FROM `$candidateTable`");

if (!$colRes) {
    echo json_encode([
        'success' => false,
        'message' => 'Candidate table not found for project'
    ]);
    exit;
}

while ($col = mysqli_fetch_assoc($colRes)) {
    if (!in_array($col['Field'], ['id', 'created_at', 'updated_at'])) {
        $db_columns[] = $col['Field'];
    }
}

echo json_encode([
    'success'    => true,
    'project_id' => $project_id,
    'headers'    => $excel_headers,
    'db_columns' => $db_columns
]);
exit;



    /* case 'importData':
        
        $mapping = $_POST['mapping'] ?? [];
        $project_id = $_POST['project_id'] ?? null;
        $excel_data = $_SESSION['excel_data'] ?? [];
        if(!$project_id || empty($mapping) || empty($excel_data)) {
            echo json_encode(['success'=>false,'message'=>'Invalid request']); exit;
        }

        $candidateTable = $project_id . '_candidate_details';
        $dbColumns = array_values(array_filter($mapping));
        if(empty($dbColumns)) { echo json_encode(['success'=>false,'message'=>'No columns mapped']); exit; }
        $placeholders = implode(',', array_fill(0,count($dbColumns),'?'));
        $colNames = implode(',', $dbColumns);
       echo $stmt = $con1->prepare("INSERT INTO `$candidateTable` ($colNames) VALUES ($placeholders)");

        foreach($excel_data as $row) {
            $values = [];
            foreach($mapping as $excelCol => $dbCol) {
                if($dbCol) {
                    $index = array_search($excelCol, $_SESSION['excel_headers']);
                    $values[] = $row[$index] ?? null;
                }
            }
            $types = str_repeat('s', count($values));
            $stmt->bind_param($types,...$values);
            $stmt->execute();
        }

        echo json_encode(['success'=>true,'message'=>'Data imported successfully']);
    break; */
    case 'importData':

    $mapping     = $_POST['mapping'] ?? [];
    $project_id  = $_POST['project_id'] ?? null;
    $excel_data  = $_SESSION['excel_data'] ?? [];
    $headers     = $_SESSION['excel_headers'] ?? [];

    if (!$project_id || empty($mapping) || empty($excel_data)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    /* ==========================================================
       1️⃣ CHECK: Duplicate DB column mapping
       ========================================================== */
    $mappedColumns = array_filter($mapping); // remove empty
    if (count($mappedColumns) !== count(array_unique($mappedColumns))) {
        echo json_encode([
            'success' => false,
            'message' => 'Duplicate DB column mapping is not allowed'
        ]);
        exit;
    }

    /* ==========================================================
       2️⃣ IDENTIFY RegID COLUMN
       ========================================================== */
    $regidExcelColumn = array_search('Regid', $mapping);
    if ($regidExcelColumn === false) {
        echo json_encode([
            'success' => false,
            'message' => 'RegID must be mapped'
        ]);
        exit;
    }

    $regidIndex = array_search($regidExcelColumn, $headers);
echo $regidIndex;exit;
    /* ==========================================================
       3️⃣ CHECK: Duplicate RegID inside Excel
       ========================================================== */
    $excelRegIds = [];

    foreach ($excel_data as $row) {
        $regid = trim($row[$regidIndex] ?? '');
        if ($regid === '') continue;

        if (in_array($regid, $excelRegIds)) {
            echo json_encode([
                'success' => false,
                'message' => "Duplicate RegID found in Excel: $regid"
            ]);
            exit;
        }
        $excelRegIds[] = $regid;
    }

    echo $excelRegIds;exit;

    /* ==========================================================
       4️⃣ CHECK: Duplicate RegID in Database
       ========================================================== */
    $candidateTable = $project_id . '_candidate_details';

    $placeholders = implode(',', array_fill(0, count($excelRegIds), '?'));
    $checkSql = "SELECT Regid FROM `$candidateTable` WHERE Regid IN ($placeholders)";
    $checkStmt = $con1->prepare($checkSql);
    $types = str_repeat('s', count($excelRegIds));
    $checkStmt->bind_param($types, ...$excelRegIds);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
print_r($result);exit;
    if ($result->num_rows > 0) {
        $existing = [];
        while ($r = $result->fetch_assoc()) {
            $existing[] = $r['Regid'];
        }
        echo json_encode([
            'success' => false,
            'message' => 'RegID already exists: ' . implode(', ', $existing)
        ]);
        exit;
    }

    /* ==========================================================
       5️⃣ START TRANSACTION (ALL OR NOTHING)
       ========================================================== */
    $con1->begin_transaction();

    try {
        $dbColumns    = array_values($mappedColumns);
        $colNames     = implode(',', $dbColumns);
        $placeholders = implode(',', array_fill(0, count($dbColumns), '?'));

        $stmt = $con1->prepare(
            "INSERT INTO `$candidateTable` ($colNames) VALUES ($placeholders)"
        );

        foreach ($excel_data as $row) {
            $values = [];

            foreach ($mapping as $excelCol => $dbCol) {
                if ($dbCol) {
                    $idx = array_search($excelCol, $headers);
                    $values[] = $row[$idx] ?? null;
                }
            }

            $types = str_repeat('s', count($values));
            $stmt->bind_param($types, ...$values);

            if (!$stmt->execute()) {
                throw new Exception('Insert failed');
            }
        }

        /* ==========================================================
           6️⃣ COMMIT (SUCCESS)
           ========================================================== */
        $con1->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Candidates imported successfully'
        ]);

    } catch (Exception $e) {

        /* ==========================================================
           7️⃣ ROLLBACK (FAILURE)
           ========================================================== */
        $con1->rollback();

        echo json_encode([
            'success' => false,
            'message' => 'Import failed. No data was saved.'
        ]);
    }

break;

    }
exit;
