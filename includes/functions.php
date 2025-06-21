<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function uploadImage($file, $target_dir = "../public/uploads/") {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) return false;
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return 'uploads/' . $filename;
    }
    return false;
}

function generateWhatsAppLink($phone, $name) {
    $message = "Hi $name, I saw your profile on WakaziLink";
    return "https://wa.me/$phone?text=" . urlencode($message);
}
?>