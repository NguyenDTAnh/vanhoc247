<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'] ?? '';

    if (empty($userMessage)) {
        echo json_encode(['status' => 'error', 'reply' => 'Nội dung trống!']);
        exit;
    }

    // ==========================================
    // 1. CẤU HÌNH API CHÍNH
    // Đổi giá trị biến này 'gemini' hoặc 'openai'
    // ==========================================
    $api_provider = "gemini"; // <--- CHỈ CẦN THAY ĐỔI Ở ĐÂY LÀ ĐỦ

    if ($api_provider === "gemini") {
        // --- VERSION 1: GOOGLE GEMINI API ---
        $apiKey = "AIzaSyDZOnXGbR7r3XvZ6JnSt0RiQH_XMILxwYc"; 
        $modelName = "gemini-3-flash-preview";
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
    } else {
        // --- VERSION 2: LOCAL OPENAI API ---
        $url = "http://127.0.0.1:8045/v1/chat/completions";
        $modelName = "gemini-3-flash-preview"; // Tên model của Local LLM
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
    }

    // ==========================================
    // 2. GỬI REQUEST CURL
    // ==========================================
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
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
    if ($api_provider === "gemini") {
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $finalReply = $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            $lastError = isset($result['error']['message']) ? $result['error']['message'] : 'Model không phản hồi hoặc JSON lỗi. (Gemini)';
        }
    } else {
        if (isset($result['choices'][0]['message']['content'])) {
            $finalReply = $result['choices'][0]['message']['content'];
        } else {
            $lastError = isset($result['error']['message']) ? $result['error']['message'] : 'Model không phản hồi hoặc JSON lỗi. (OpenAI)';
        }
    }

    // ==========================================
    // 4. TRẢ KẾT QUẢ VỀ FRONTEND
    // ==========================================
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
