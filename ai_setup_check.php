<?php
/**
 * AI Setup Check Script for Moodle
 * Script kiểm tra và hướng dẫn cấu hình AI trong Moodle
 */

require_once('config.php');
require_once($CFG->libdir . '/adminlib.php');

// Kiểm tra quyền admin
require_admin();

echo "<h1>Moodle AI Setup Check</h1>";
echo "<h2>Kiểm tra cấu hình AI trong Moodle</h2>";

// 1. Kiểm tra AI providers
echo "<h3>1. AI Providers</h3>";
$manager = \core\di::get(\core_ai\manager::class);
$providers = $manager->get_provider_instances();

if (empty($providers)) {
    echo "<p style='color: red;'>❌ Chưa có AI provider nào được cấu hình</p>";
    echo "<p><strong>Hướng dẫn:</strong> Vào <a href='/admin/settings.php?section=aiprovider'>AI Providers</a> để thêm provider</p>";
} else {
    echo "<p style='color: green;'>✅ Đã có " . count($providers) . " AI provider(s) được cấu hình:</p>";
    echo "<ul>";
    foreach ($providers as $provider) {
        $status = $provider->enabled ? "✅ Enabled" : "❌ Disabled";
        echo "<li><strong>{$provider->name}</strong> - {$status}</li>";
    }
    echo "</ul>";
}

// 2. Kiểm tra AI placements
echo "<h3>2. AI Placements</h3>";
$pluginmanager = core_plugin_manager::instance();
$placements = $pluginmanager->get_plugins_of_type('aiplacement');

$enabled_placements = 0;
foreach ($placements as $placement) {
    if ($placement->is_enabled()) {
        $enabled_placements++;
    }
}

if ($enabled_placements == 0) {
    echo "<p style='color: red;'>❌ Chưa có AI placement nào được kích hoạt</p>";
    echo "<p><strong>Hướng dẫn:</strong> Vào <a href='/admin/settings.php?section=aiplacement'>AI Placements</a> để kích hoạt</p>";
} else {
    echo "<p style='color: green;'>✅ Đã có {$enabled_placements} AI placement(s) được kích hoạt</p>";
}

// 3. Kiểm tra database tables
echo "<h3>3. Database Tables</h3>";
$tables = ['ai_provider_instances', 'ai_placement_actions', 'ai_usage_logs'];
$db = $DB;

foreach ($tables as $table) {
    if ($db->get_manager()->table_exists($table)) {
        echo "<p style='color: green;'>✅ Table '{$table}' tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Table '{$table}' không tồn tại</p>";
    }
}

// 4. Kiểm tra capabilities
echo "<h3>4. AI Capabilities</h3>";
$capabilities = [
    'moodle/ai:useai',
    'moodle/ai:viewaipolicyacceptancereport',
    'moodle/ai:viewaiusagereport'
];

foreach ($capabilities as $capability) {
    if (get_capability_info($capability)) {
        echo "<p style='color: green;'>✅ Capability '{$capability}' tồn tại</p>";
    } else {
        echo "<p style='color: red;'>❌ Capability '{$capability}' không tồn tại</p>";
    }
}

// 5. Kiểm tra cấu hình system
echo "<h3>5. System Configuration</h3>";

// Kiểm tra cURL
if (function_exists('curl_init')) {
    echo "<p style='color: green;'>✅ cURL extension có sẵn</p>";
} else {
    echo "<p style='color: red;'>❌ cURL extension không có sẵn - cần thiết cho AI providers</p>";
}

// Kiểm tra JSON
if (function_exists('json_encode')) {
    echo "<p style='color: green;'>✅ JSON extension có sẵn</p>";
} else {
    echo "<p style='color: red;'>❌ JSON extension không có sẵn</p>";
}

// 6. Hướng dẫn cấu hình
echo "<h3>6. Hướng dẫn cấu hình nhanh</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba;'>";
echo "<h4>Bước 1: Thêm AI Provider</h4>";
echo "<ol>";
echo "<li>Truy cập: <a href='/admin/settings.php?section=aiprovider' target='_blank'>AI Providers</a></li>";
echo "<li>Click 'Add new provider'</li>";
echo "<li>Chọn provider (OpenAI, Ollama, hoặc Azure AI)</li>";
echo "<li>Điền thông tin cấu hình</li>";
echo "</ol>";

echo "<h4>Bước 2: Kích hoạt AI Placements</h4>";
echo "<ol>";
echo "<li>Truy cập: <a href='/admin/settings.php?section=aiplacement' target='_blank'>AI Placements</a></li>";
echo "<li>Kích hoạt 'Course Assist' và 'Editor'</li>";
echo "<li>Cấu hình các tính năng</li>";
echo "</ol>";

echo "<h4>Bước 3: Thiết lập Policy</h4>";
echo "<ol>";
echo "<li>Truy cập: <a href='/ai/policy_acceptance_report.php' target='_blank'>AI Policy</a></li>";
echo "<li>Thiết lập chính sách sử dụng AI</li>";
echo "<li>Yêu cầu người dùng chấp nhận</li>";
echo "</ol>";
echo "</div>";

// 7. Test AI functionality
echo "<h3>7. Test AI Functionality</h3>";
if (!empty($providers)) {
    echo "<p><a href='/ai/usage_report.php' target='_blank'>📊 Xem AI Usage Report</a></p>";
    echo "<p><a href='/ai/policy_acceptance_report.php' target='_blank'>📋 Xem Policy Acceptance Report</a></p>";
} else {
    echo "<p style='color: orange;'>⚠️ Cần cấu hình AI provider trước khi test</p>";
}

echo "<hr>";
echo "<p><em>Script này giúp kiểm tra cấu hình AI trong Moodle. Chạy lại script sau khi cấu hình để kiểm tra.</em></p>";
?>
