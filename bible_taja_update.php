<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// POST 데이터 읽기 및 파싱
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['accuracy']) || !isset($input['speed'])) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid input"));
    exit;
}

$accuracy = floatval($input['accuracy']);
$speed = floatval($input['speed']);

// 점수 계산: 정확도 + (속도 × 2)
$score = round($accuracy + $speed * 2, 2);

// DB 연결 정보
$host = 'localhost';
$dbname = '';
$user = '';
$pass = '';

try {
    $pdo = new PDO("mysql:host=".$host.";dbname=".$dbname.";charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 점수 저장
    $stmt = $pdo->prepare("INSERT INTO typing_scores (accuracy, speed, score, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute(array($accuracy, $speed, $score));

    echo json_encode(array("message" => "기록이 저장되었습니다. 점수: ".$score));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => "DB 오류: " . $e->getMessage()));
}



