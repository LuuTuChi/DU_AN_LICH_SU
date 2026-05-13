<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['answer' => 'Vui lòng đăng nhập nhé!']);
    exit();
}

$question = $_POST['question'] ?? '';
$lesson_name = $_POST['lesson_name'] ?? 'Lịch sử 12';
$apiKey = 'AIzaSyAHoQNun-I6qnNEciU_VOcTx1zdfbYa9pM'; 

// SỬA LỖI MODEL NOT FOUND: Dùng URL v1beta và model CHUẨN tên đầy đủ
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

$parts = [];
$parts[] = ["text" => "Bạn là Trợ lý Sử Việt. Ngữ cảnh: $lesson_name. Câu hỏi: $question"];

// Xử lý ảnh
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $parts[] = [
        "inline_data" => [
            "mime_type" => $_FILES['file']['type'],
            "data" => base64_encode(file_get_contents($_FILES['file']['tmp_name']))
        ]
    ];
}

// CẤU TRÚC GỬI ĐI TỐI GIẢN (Để né lỗi cấu trúc mảng lồng nhau)
$data = [
    "contents" => [
        ["parts" => $parts]
    ]
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Vượt lỗi SSL XAMPP
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$result = json_decode($response, true);
curl_close($ch);

// LẤY CÂU TRẢ LỜI (Dùng optional chaining logic của PHP)
if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $answer = $result['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['answer' => $answer], JSON_UNESCAPED_UNICODE);
} else {
    // Nếu vẫn lỗi, bung bét hết dữ liệu ra để nhìn trực diện lỗi ở đâu
    $msg = $result['error']['message'] ?? 'Lỗi không xác định từ Google';
    echo json_encode(['answer' => "⚠️ Chú ý: " . $msg . ". (Gợi ý: Nếu báo User location, hãy dùng VPN sang Mỹ)"]);
}