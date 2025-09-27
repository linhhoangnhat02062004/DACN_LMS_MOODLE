<?php
/**
 * AI Demo Script for Moodle
 * Script demo các tính năng AI trong Moodle
 */

require_once('config.php');
require_login();

// Kiểm tra quyền sử dụng AI
$context = context_system::instance();
if (!has_capability('moodle/ai:useai', $context)) {
    throw new moodle_exception('nopermission', 'core_ai');
}

echo "<h1>Moodle AI Demo</h1>";
echo "<h2>Demo các tính năng AI</h2>";

// Lấy AI manager
$manager = \core\di::get(\core_ai\manager::class);
$providers = $manager->get_provider_instances();

if (empty($providers)) {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<h3>⚠️ Chưa có AI Provider</h3>";
    echo "<p>Vui lòng cấu hình AI provider trước khi sử dụng demo này.</p>";
    echo "<p><a href='/admin/settings.php?section=aiprovider'>Cấu hình AI Provider</a></p>";
    echo "</div>";
    exit;
}

echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<h3>✅ AI Providers có sẵn</h3>";
echo "<ul>";
foreach ($providers as $provider) {
    echo "<li><strong>{$provider->name}</strong> - " . ($provider->enabled ? "Enabled" : "Disabled") . "</li>";
}
echo "</ul>";
echo "</div>";

// Demo form
echo "<h3>Demo AI Actions</h3>";
echo "<form method='post' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";

// Chọn provider
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='provider'><strong>Chọn AI Provider:</strong></label><br>";
echo "<select name='provider' id='provider' style='width: 300px; padding: 5px;'>";
foreach ($providers as $provider) {
    if ($provider->enabled) {
        echo "<option value='{$provider->id}'>{$provider->name}</option>";
    }
}
echo "</select>";
echo "</div>";

// Chọn action
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='action'><strong>Chọn AI Action:</strong></label><br>";
echo "<select name='action' id='action' style='width: 300px; padding: 5px;'>";
echo "<option value='generate_text'>Generate Text (Tạo văn bản)</option>";
echo "<option value='summarise_text'>Summarise Text (Tóm tắt văn bản)</option>";
echo "<option value='explain_text'>Explain Text (Giải thích văn bản)</option>";
echo "<option value='generate_image'>Generate Image (Tạo hình ảnh)</option>";
echo "</select>";
echo "</div>";

// Input text
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='input_text'><strong>Input Text:</strong></label><br>";
echo "<textarea name='input_text' id='input_text' rows='4' cols='80' placeholder='Nhập văn bản cần xử lý...'>";
if (isset($_POST['input_text'])) {
    echo htmlspecialchars($_POST['input_text']);
}
echo "</textarea>";
echo "</div>";

// Prompt cho generate text
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='prompt'><strong>Prompt (cho Generate Text):</strong></label><br>";
echo "<input type='text' name='prompt' id='prompt' style='width: 100%; padding: 5px;' placeholder='Nhập prompt cho AI...' value='";
if (isset($_POST['prompt'])) {
    echo htmlspecialchars($_POST['prompt']);
}
echo "'>";
echo "</div>";

echo "<button type='submit' name='submit' style='background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>🚀 Chạy AI Action</button>";
echo "</form>";

// Xử lý form
if (isset($_POST['submit'])) {
    $provider_id = required_param('provider', PARAM_INT);
    $action = required_param('action', PARAM_TEXT);
    $input_text = optional_param('input_text', '', PARAM_TEXT);
    $prompt = optional_param('prompt', '', PARAM_TEXT);
    
    echo "<hr>";
    echo "<h3>Kết quả AI</h3>";
    
    try {
        // Lấy provider instance
        $provider_instance = $manager->get_provider_instances(['id' => $provider_id]);
        if (empty($provider_instance)) {
            throw new moodle_exception('Provider not found');
        }
        $provider_instance = reset($provider_instance);
        
        // Tạo action instance
        $action_class = "\\core_ai\\aiactions\\{$action}";
        if (!class_exists($action_class)) {
            throw new moodle_exception('Action class not found: ' . $action_class);
        }
        
        $action_instance = new $action_class();
        
        // Chuẩn bị data
        $data = [];
        if ($action === 'generate_text') {
            $data['prompt'] = $prompt;
        } else {
            $data['text'] = $input_text;
        }
        
        // Thực hiện action
        echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007cba;'>";
        echo "<h4>🔄 Đang xử lý...</h4>";
        echo "<p><strong>Provider:</strong> {$provider_instance->name}</p>";
        echo "<p><strong>Action:</strong> {$action}</p>";
        echo "<p><strong>Input:</strong> " . htmlspecialchars($input_text ?: $prompt) . "</p>";
        echo "</div>";
        
        // Gọi AI
        $response = $action_instance->execute($provider_instance, $data);
        
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
        echo "<h4>✅ Kết quả:</h4>";
        echo "<div style='background: white; padding: 10px; border-radius: 3px; margin-top: 10px;'>";
        echo "<pre style='white-space: pre-wrap; word-wrap: break-word;'>";
        echo htmlspecialchars($response->get_content());
        echo "</pre>";
        echo "</div>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
        echo "<h4>❌ Lỗi:</h4>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
}

// Hiển thị thông tin bổ sung
echo "<hr>";
echo "<h3>Thông tin bổ sung</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<h4>📚 Các AI Actions có sẵn:</h4>";
echo "<ul>";
echo "<li><strong>Generate Text:</strong> Tạo văn bản mới dựa trên prompt</li>";
echo "<li><strong>Summarise Text:</strong> Tóm tắt văn bản dài</li>";
echo "<li><strong>Explain Text:</strong> Giải thích ý nghĩa của văn bản</li>";
echo "<li><strong>Generate Image:</strong> Tạo hình ảnh dựa trên mô tả</li>";
echo "</ul>";

echo "<h4>🔧 Cấu hình AI:</h4>";
echo "<ul>";
echo "<li><a href='/admin/settings.php?section=aiprovider'>Quản lý AI Providers</a></li>";
echo "<li><a href='/admin/settings.php?section=aiplacement'>Quản lý AI Placements</a></li>";
echo "<li><a href='/ai/usage_report.php'>Báo cáo sử dụng AI</a></li>";
echo "<li><a href='/ai/policy_acceptance_report.php'>Báo cáo chấp nhận Policy</a></li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><em>Demo này giúp bạn test các tính năng AI trong Moodle. Đảm bảo đã cấu hình AI provider trước khi sử dụng.</em></p>";
?>
