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
        $shift = $_POST['shift'] ?? null;

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

        $excludeColumns = [
            'ID',
            'project_id',
            'project_shift',
            'created_at',
            'updated_at'
        ];

        while ($col = mysqli_fetch_assoc($colRes)) {
            if (!in_array($col['Field'], $excludeColumns)) {
                $db_columns[] = $col['Field'];
            }
        }

        echo json_encode([
            'success'    => true,
            'project_id' => $project_id,
            'headers'    => $excel_headers,
            'db_columns' => $db_columns,
            'shift'      => $shift
        ]);
        exit;



    case 'importData':

        session_start();

        $mapping     = $_POST['mapping'] ?? [];
        $project_id  = $_POST['project_id'] ?? null;
        $excel_data  = $_SESSION['excel_data'] ?? [];
        $headersRaw  = $_SESSION['excel_headers'] ?? [];
        $projectshift = isset($_POST['shift']) && $_POST['shift'] !== '' ? $_POST['shift'] : 3;


        if (!$project_id || empty($mapping) || empty($excel_data) || empty($headersRaw)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $headers = array_map(function ($h) {
            return strtolower(trim($h));
        }, $headersRaw);

        $headerIndexes = array_flip($headers);

        $normalizedMapping = [];
        foreach ($mapping as $excelCol => $dbCol) {
            $normalizedMapping[strtolower(trim($excelCol))] = trim($dbCol);
        }

        $mappedColumns = array_filter($normalizedMapping);

        if (count($mappedColumns) !== count(array_unique($mappedColumns))) {
            echo json_encode([
                'success' => false,
                'message' => 'Duplicate DB column mapping is not allowed'
            ]);
            exit;
        }

        $regidExcelCol = array_search('Regid', $mappedColumns, true);

        if ($regidExcelCol === false) {
            echo json_encode([
                'success' => false,
                'message' => 'RegID must be mapped'
            ]);
            exit;
        }

        if (!isset($headerIndexes[strtolower($regidExcelCol)])) {
            echo json_encode([
                'success' => false,
                'message' => 'RegID column not found in Excel'
            ]);
            exit;
        }

        $regidIndex = $headerIndexes[strtolower($regidExcelCol)];


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


        $candidateTable = $project_id . '_candidate_details';

        if (!empty($excelRegIds)) {

            $placeholders = implode(',', array_fill(0, count($excelRegIds), '?'));
            $checkSql = "SELECT Regid FROM `$candidateTable` WHERE Regid IN ($placeholders)";
            $checkStmt = $con1->prepare($checkSql);

            $types = str_repeat('s', count($excelRegIds));
            $checkStmt->bind_param($types, ...$excelRegIds);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

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
        }

        $con1->begin_transaction();

        try {

            $dbColumns = array_values($mappedColumns);
            $dbColumns[] = 'project_shift';
            $colNames  = implode(',', $dbColumns);
            $placeholders = implode(',', array_fill(0, count($dbColumns), '?'));

            $stmt = $con1->prepare(
                "INSERT INTO `$candidateTable` ($colNames) VALUES ($placeholders)"
            );

            foreach ($excel_data as $row) {

                $values = [];

                foreach ($normalizedMapping as $excelCol => $dbCol) {

                    if (!$dbCol) continue;

                    if (!isset($headerIndexes[$excelCol])) {
                        $values[] = null;
                        continue;
                    }

                    $idx = $headerIndexes[$excelCol];
                    $values[] = $row[$idx] ?? null;
                }

                $values[] = $projectshift;

                $types = str_repeat('s', count($values));
                $stmt->bind_param($types, ...$values);

                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }
            }

            $con1->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Candidates imported successfully'
            ]);
        } catch (Exception $e) {

            $con1->rollback();

            echo json_encode([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ]);
        }

        break;


    /* case 'getCandidates':

        $project_id = $_POST['project_id'] ?? null;
        $shift      = $_POST['shift'] ?? null;

        if (!$project_id) {
            echo json_encode([
                "draw" => intval($_POST['draw']),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            exit;
        }

        $table = $project_id . "_candidate_details";

        $where = [];

        // apply shift filter ONLY if selected
        if (!empty($shift)) {
            $where[] = "project_shift = " . intval($shift);
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT * FROM `$table` $whereSql";

        $res = mysqli_query($con1, $sql);

        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }

        echo json_encode([
            "draw" => intval($_POST['draw']),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ]);
        exit; */

       case 'getCandidates':

    $project_id = $_POST['project_id'] ?? null;
    $shift      = $_POST['shift'] ?? null;
    $search     = $_POST['search']['value'] ?? '';
    $draw       = intval($_POST['draw']);
    $start      = intval($_POST['start']);
    $length     = intval($_POST['length']);

    if (!$project_id) {
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ]);
        exit;
    }

    $table = $project_id . "_candidate_details";

    $where = [];

    if (!empty($shift)) {
        $where[] = "project_shift = " . intval($shift);
    }

    // 🔍 SEARCH (IMPORTANT)
    if (!empty($search)) {
        $search = mysqli_real_escape_string($con1, $search);
        $where[] = "(Regid LIKE '%$search%'
                     OR name LIKE '%$search%'
                    )";
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // total records
    $totalRes = mysqli_query($con1, "SELECT COUNT(*) AS cnt FROM `$table`");
    $total = mysqli_fetch_assoc($totalRes)['cnt'];

    // filtered records
    $filterRes = mysqli_query($con1, "SELECT COUNT(*) AS cnt FROM `$table` $whereSql");
    $filtered = mysqli_fetch_assoc($filterRes)['cnt'];

    // paginated data
    $sql = "SELECT * FROM `$table` $whereSql LIMIT $start, $length";
    $res = mysqli_query($con1, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $filtered,
        "data" => $data
    ]);
    exit;


}
exit;
