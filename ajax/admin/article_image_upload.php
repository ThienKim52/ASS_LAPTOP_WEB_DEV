<?php
// ajax/admin/article_image_upload.php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$targetDir = "../../assets/img/posts/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Allowed origins to upload from
$accepted_origins = array("http://localhost");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // same-origin requests won't set an origin. If the origin is set, it must be valid.
    if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
        header("HTTP/1.1 403 Origin Denied");
        return;
    }
}

// Don't attempt to create the image if there is no file
if (empty($_FILES['file'])) {
    header("HTTP/1.1 400 Bad Request");
    return;
}

$file = $_FILES['file'];
$fileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
$targetFile = $targetDir . $fileName;

// Check file type
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
$allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'webp');

if (!in_array($imageFileType, $allowedTypes)) {
    header("HTTP/1.1 400 Invalid file type");
    return;
}

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode(array('location' => 'assets/img/posts/' . $fileName));
} else {
    header("HTTP/1.1 500 Server Error");
}
?>
