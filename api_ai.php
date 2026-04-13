<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'] ?? '';

    if (empty($userMessage)) {
        echo json_encode(['status' => 'error', 'reply' => 'Nội dung trống!']);
        exit;
    }

    // ==========================================
    // 1. CẤU HÌNH API (Chọn 1 trong 2 version)
    // ==========================================

    /* 
     * VERSION 1: GOOGLE GEMINI API (ĐANG ĐƯỢC ƯU TIÊN SỬ DỤNG)
     * Đây là format payload chuẩn của Google server
     */
    $apiKey = "AIzaSyDZOnXGbR7r3XvZ6JnSt0RiQH_XMILxwYc"; 
    $modelName = "gemini-2.5-flash";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $apiKey;
    
    $data = [
        "systemInstruction" => [
            "parts" => [
                ["text" => "Bạn là 1 Giáo viên chuyên nghiệp, giỏi toàn diện và có kiến thức sâu đậm về tất cả các môn học. Nhiệm vụ của bạn là hướng dẫn user học, giải đáp các thắc mắc, bài học cho user. Tên bạn là Muse AI."]
            ]
        ],
        "contents" => [
            [
                "parts" => [
                    ["text" => $userMessage]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7
        ]
    ];

    /* 
     * VERSION 2: LOCAL OPENAI API (XÓA COMMENT ĐỂ DÙNG)
     * Dùng cho Local LLM (OpenCode, LMStudio, v.v...) tương thích cấu trúc OpenAI
     */
    /*
    $url = "http://127.0.0.1:8045/v1/chat/completions";
    $modelName = "gemini-3-flash-preview"; 
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
    */

    // ==========================================
    // 2. GỬI REQUEST CURL
    // ==========================================
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
        // 'Authorization: Bearer optional_key_here' // Bật lên nếu dùng OpenAI local có auth
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); 

    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    $finalReply = "";
    $lastError = "";

    // ==========================================
    // 3. PARSE RESPONSE TỪ API
    // ==========================================

    // --> Parse cho VERSION 1 (Gemini API)
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $finalReply = $result['candidates'][0]['content']['parts'][0]['text'];
    } else {
        $lastError = isset($result['error']['message']) ? $result['error']['message'] : 'Model không phản hồi hoặc sai định dạng JSON.';
    }

    // --> Parse cho VERSION 2 (OpenAI format)
    /*
    if (isset($result['choices'][0]['message']['content'])) {
        $finalReply = $result['choices'][0]['message']['content'];
    } else {
        $lastError = isset($result['error']['message']) ? $result['error']['message'] : 'Model không phản hồi hoặc sai định dạng JSON. Cấu trúc trả về: ' . json_encode($result);
    }
    */

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
