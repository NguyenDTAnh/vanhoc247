<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'] ?? '';
    /*
    $apiKey = "AIzaSyAMWUy0lVmRbbgXaqBZt9Z5lioqIAVxYHw"; 

    if (empty($userMessage)) {
        echo json_encode(['status' => 'error', 'reply' => 'Nội dung trống!']);
        exit;
    }

    // Force v1beta for all new models to ensure stability
    $modelName = "gemini-3-flash-preview";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $apiKey;
    */

    // New Local API Endpoint (OpenAI compatible)
    $url = "http://127.0.0.1:8045/v1/chat/completions";
    $modelName = "gemini-3-flash-preview"; // Tên model tùy thuộc vào local server của bạn
    
    // Cấu trúc dữ liệu theo chuẩn OpenAI/Local LLM
    $data = [
        "model" => $modelName,
        "messages" => [
            [
                "role" => "system",
                "content" => "Bạn là 1 Giáo viên chuyên nghiệp, giỏi toàn diện và có kiến thức sâu đậm về tất cả các môn học. Nhiệm vụ của bạn là hướng dẫn user học, giải đáp các thắc mắc, bài học cho user. Tên bạn là Muse AI."
            ],
            [
                "role" => "user",
                "content" => $userMessage
            ]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
        // 'Authorization: Bearer optional_key_here' // Thêm nếu server yêu cầu auth
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Tăng timeout cho local models

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    $finalReply = "";
    $lastError = "";

    // Parse response theo chuẩn OpenAI
    if (isset($result['choices'][0]['message']['content'])) {
        $finalReply = $result['choices'][0]['message']['content'];
    } else {
        $lastError = isset($result['error']['message']) ? $result['error']['message'] : 'Model không phản hồi hoặc sai định dạng JSON.';
    }

    if (!empty($finalReply)) {
        echo json_encode([
            'status' => 'success',
            'reply' => $finalReply
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'reply' => 'Lỗi hệ thống: ' . $lastError
        ]);
    }
}