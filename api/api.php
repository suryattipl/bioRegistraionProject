<?php
require_once '../config.php';

header('Content-Type: application/json');
error_reporting(0);

// $action = $_GET['action'] ?? '';
$action = $_REQUEST['action'] ?? '';

switch ($action) {


    case 'login':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(["error" => "Missing username or password"]);
            exit;
        }
        $username = preg_replace('/[^a-zA-Z0-9_.@-]/', '', $username);
        $password = trim($password);

        $username = mysqli_real_escape_string($con1, $username);
        $password = mysqli_real_escape_string($con1, $password);

        $sql = "SELECT * FROM members_login WHERE username = ? AND password = ?";
        $stmt = $con1->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user) {
            session_start();
            session_regenerate_id(true);
            $_SESSION['username'] = $user['username'];
            $_SESSION['id'] = $user['id'];

            echo json_encode([
                "success" => true,
                "message" => "Login Successful",
                "role" => $user['role'],
                "username" => $user['username']
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Invalid credentials"
            ]);
        }
        break;

    /* case 'getProjects':
        $res = mysqli_query($con1, "SELECT * FROM projects ORDER BY id DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(["data" => $data]);
    break; */
    case 'getProjects':

    $sql = "
        SELECT 
            p.*,
            m.name AS created_by_name
        FROM projects p
        LEFT JOIN members_login m ON m.id = p.created_by
        ORDER BY p.id DESC
    ";

    $res = mysqli_query($con1, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["data" => $data]);
    break;




    case 'createProject':
        // echo $_POST;exit;
        if (
            empty($_POST['projectname']) ||
            empty($_POST['projectdate']) ||
            empty($_POST['same_candidates']) ||
            empty($_POST['shift_ids'])
        ) {
            http_response_code(400);
            exit('Invalid input');
        }

        $projectname = trim($_POST['projectname']);
        $projectdate = $_POST['projectdate'];
        $shiftIds    = $_POST['shift_ids']; 
        $created_by  = $_POST['created_by'];
        $same_candidates_in_shifts = $_POST['same_candidates'];
        $morning_shift = 0;
        $evening_shift = 0;

        $shiftQuery = mysqli_query(
            $con1,
            "SELECT id, exam_shifts FROM shifts WHERE id IN (" .
                implode(',', array_map('intval', $shiftIds)) . ")"
        );

        while ($row = mysqli_fetch_assoc($shiftQuery)) {
            if (strtolower($row['exam_shifts']) === 'morning') {
                $morning_shift = 1;
            }
            if (strtolower($row['exam_shifts']) === 'evening') {
                $evening_shift = 1;
            }
        }

        $stmt = $con1->prepare(
            "INSERT INTO projects
        (projectname, projectdates, morning_shift, evening_shift, same_candidates_in_shifts, created_by, create_date_time)
        VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );

        $stmt->bind_param(
            "ssiiii",
            $projectname,
            $projectdate,
            $morning_shift,
            $evening_shift,
            $same_candidates_in_shifts,
            $created_by,
            // $_SESSION['username']
        );

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Project updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Update failed"
            ]);
        }
        break;





    case 'updateProject':

        if (
            empty($_POST['id']) ||
            empty($_POST['projectname']) ||
            empty($_POST['projectdate']) ||
            empty($_POST['shift_ids'])
        ) {
            http_response_code(400);
            exit('Invalid input');
        }

        $id          = (int)$_POST['id'];
        $projectname = trim($_POST['projectname']);
        $projectdate = $_POST['projectdate'];
        $shiftIds    = $_POST['shift_ids']; 

        $morning_shift = 0;
        $evening_shift = 0;

        $shiftQuery = mysqli_query(
            $con1,
            "SELECT id, exam_shifts FROM shifts 
     WHERE id IN (" . implode(',', array_map('intval', $shiftIds)) . ")"
        );

        while ($row = mysqli_fetch_assoc($shiftQuery)) {
            if (strtolower($row['exam_shifts']) === 'morning') {
                $morning_shift = 1;
            }
            if (strtolower($row['exam_shifts']) === 'evening') {
                $evening_shift = 1;
            }
        }

        $con1->begin_transaction();

        try {

            $stmt = $con1->prepare(
                "UPDATE projects 
         SET projectname=?, projectdates=?, morning_shift=?, evening_shift=? 
         WHERE id=?"
            );
            $stmt->bind_param(
                "ssiii",
                $projectname,
                $projectdate,
                $morning_shift,
                $evening_shift,
                $id
            );
            $stmt->execute();
            $con1->commit();
            echo json_encode([
                "success" => true,
                "message" => "Project updated successfully"
            ]);
        } catch (Exception $e) {
            $con1->rollback();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Update failed"
            ]);
        }

        break;




    case 'deleteProject':
        if (empty($_POST['id'])) {
            http_response_code(400);
            echo "Invalid project ID";
            exit;
        }

        $id = $_POST['id'];

        $stmt = $con1->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Project Deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Deleted failed"
            ]);
        }
        break;



    default:
        echo json_encode(["error" => "Invalid request or unknown action"]);
        break;
}

exit;
