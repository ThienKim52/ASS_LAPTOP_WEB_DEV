<?php
session_start();
require_once '../models/ContactModel.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? 'create';

try {
    $contactModel = new ContactModel();
    
    switch ($action) {
        case 'create':
            // PUBLIC: Submit contact form
            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            // Server-side validation
            $errors = [];
            if (empty($name) || mb_strlen($name) < 2) {
                $errors[] = "Name must be at least 2 characters.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email is required.";
            }
            if (empty($message) || mb_strlen($message) < 10) {
                $errors[] = "Message must be at least 10 characters.";
            }

            if (!empty($errors)) {
                echo json_encode(["success" => false, "message" => implode(' ', $errors)]);
                exit;
            }

            if ($contactModel->create($name, $email, $subject, $message)) {
                echo json_encode(["success" => true, "message" => "Message sent successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to submit contact."]);
            }
            break;

        case 'list':
            // ADMIN: Paginated list
            if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 10)));
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';

            $result = $contactModel->getPaginated($page, $limit, $search, $status);
            echo json_encode(["success" => true, "data" => $result]);
            break;

        case 'get':
            // ADMIN: Get single contact
            if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
                exit;
            }

            $contact = $contactModel->getById($id);
            if (!$contact) {
                echo json_encode(['success' => false, 'message' => 'Contact not found']);
                exit;
            }

            echo json_encode(["success" => true, "data" => $contact]);
            break;

        case 'updateStatus':
            // ADMIN: Update contact status
            if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
            $status = $data['status'] ?? '';

            if ($id <= 0 || empty($status)) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
                exit;
            }

            if ($contactModel->updateStatus($id, $status)) {
                echo json_encode(["success" => true, "message" => "Status updated!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update status"]);
            }
            break;

        case 'delete':
            // ADMIN: Delete contact
            if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
                exit;
            }

            if ($contactModel->delete($id)) {
                echo json_encode(["success" => true, "message" => "Contact deleted!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete contact"]);
            }
            break;

        default:
            echo json_encode([
                "success" => false,
                "message" => "Invalid action"
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
