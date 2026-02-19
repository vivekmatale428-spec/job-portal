<?php
/**
 * Image Upload Helper
 */
function upload_image($file, $type = 'profile') {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error.'];
    }

    $allowed = ALLOWED_IMAGE_TYPES;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPEG, PNG, GIF, WebP allowed.'];
    }

    if ($file['size'] > MAX_IMAGE_SIZE) {
        return ['success' => false, 'error' => 'File too large. Max 1MB allowed.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $dir = $type === 'logo' ? LOGO_PATH : PROFILE_PATH;
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = $type . '_' . time() . '_' . uniqid() . '.' . $ext;
    $path = $dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return ['success' => true, 'filename' => $filename, 'path' => $path];
    }

    return ['success' => false, 'error' => 'Failed to upload file.'];
}

function delete_image($filename, $type = 'profile') {
    $dir = $type === 'logo' ? LOGO_PATH : PROFILE_PATH;
    $path = $dir . $filename;
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}
