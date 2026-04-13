<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'] ?? '';
    $apiKey = "AIzaSyAMWUy0lVmRbbgXaqBZt9Z5lioqIAVxYHw"; 

    if (empty($userMessage)) {
        echo json_encode(['status' => 'error', 'reply' => 'Nội dung trống!']);
        exit;
    }

    // Danh sách các model để thử (phòng trường hợp lỗi "Not Found")
    $models = [
        "gemini-1.5-flash",
        "gemini-1.5-pro",
        "gemini-pro"
    ];

    $finalReply = "";

    foreach ($models as $modelName) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $apiKey;
        
        $data = [
            "contents" => [["parts" => [["text" => "Bạn là trợ lý ảo Vanhoc247. Trả lời: " . $userMessage]]]]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $result = json_decode($response, true);

        // Nếu có phản hồi hợp lệ thì dừng lại và lấy dữ liệu
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $finalReply = $result['candidates'][0]['content']['parts'][0]['text'];
            break; 
        }
    }

    if (!empty($finalReply)) {
        echo json_encode([
            'status' => 'success',
            'reply' => $finalReply
        ]);
    } else {
        // Nếu tất cả các model đều thất bại, trả về lỗi chi tiết từ model cuối cùng để debug
        $debugError = isset($result['error']['message']) ? $result['error']['message'] : 'Không xác định';
        echo json_encode([
            'status' => 'error',
            'reply' => 'Lỗi hệ thống: ' . $debugError
        ]);
    }
}