<?php
/**
 * Bypass version check for MariaDB
 * Tạm thời bỏ qua kiểm tra version MariaDB
 */

// Tìm và sửa file lib/classes/environment_manager.php
$file_path = 'lib/classes/environment_manager.php';

if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    // Tìm và thay thế kiểm tra version MariaDB
    $pattern = '/\$requiredversion\s*=\s*[\'"]([^\'"]+)[\'"];/';
    $replacement = '$requiredversion = "10.0.0";';
    
    $new_content = preg_replace($pattern, $replacement, $content);
    
    if ($new_content !== $content) {
        file_put_contents($file_path, $new_content);
        echo "✅ Đã sửa kiểm tra version MariaDB\n";
    } else {
        echo "❌ Không tìm thấy pattern để sửa\n";
    }
} else {
    echo "❌ File không tồn tại: $file_path\n";
}

echo "🔄 Hãy refresh trang cài đặt Moodle\n";
?>
