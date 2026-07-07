<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/line_messaging.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$body = file_get_contents('php://input') ?: '';
$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'] ?? '';

lineOaLog("webhook {$method} body_len=" . strlen($body));

if ($body === '') {
    http_response_code(200);
    echo 'OK';
    exit;
}

if (!verifyLineWebhookSignature($body, $signature)) {
    $secretLen = strlen(lineOaChannelSecret());
    lineOaLog("webhook rejected: invalid signature (secret_len={$secretLen}, sig=" . ($signature !== '' ? 'yes' : 'no') . ')');
    http_response_code(403);
    exit('invalid signature');
}

$payload = json_decode($body, true);
if (!is_array($payload)) {
    http_response_code(400);
    exit('bad request');
}

foreach ($payload['events'] ?? [] as $event) {
    $type = $event['type'] ?? '';
    $replyToken = $event['replyToken'] ?? '';
    $source = $event['source'] ?? [];
    $lineUserId = (string) ($source['userId'] ?? '');

    lineOaLog('event ' . $type . ' user=' . $lineUserId);

    if ($type === 'follow' && $lineUserId !== '') {
        lineReplyMessage(
            $replyToken,
            "สวัสดีค่ะ ยินดีต้อนรับสู่ Wenxin Chinese 🎓\n\n"
            . "เชื่อมบัญชีนักเรียน: ส่งเบอร์โทรที่ใช้สมัครเรียน (เช่น 0812345678)\n"
            . "ระบบจะส่งแจ้งเตือนการจองคลาสและลิงก์ Zoom ให้ทางนี้ค่ะ"
        );
        continue;
    }

    if ($type === 'unfollow' && $lineUserId !== '') {
        unlinkLineUser($lineUserId);
        continue;
    }

    if ($type === 'message' && ($event['message']['type'] ?? '') === 'text' && $lineUserId !== '') {
        $text = trim((string) ($event['message']['text'] ?? ''));
        $digits = preg_replace('/\D/', '', $text);

        if ($digits !== '' && strlen($digits) >= 9) {
            if (linkLineUserByPhone($lineUserId, $text)) {
                lineReplyMessage(
                    $replyToken,
                    "เชื่อมบัญชีเรียบร้อยแล้วค่ะ ✅\n"
                    . "จะแจ้งเตือนการจองคลาสและลิงก์ Zoom ทาง LINE นี้\n\n"
                    . "ดูการจองได้ที่เว็บ → บัญชีของฉัน → การจองคลาส"
                );
            } else {
                lineReplyMessage(
                    $replyToken,
                    "ไม่พบเบอร์นี้ในระบบค่ะ\n"
                    . "กรุณาสมัครเรียนที่เว็บก่อน หรือตรวจสอบว่าใช้เบอร์เดียวกับตอนสมัคร\n"
                    . "หากยังไม่ได้ ติดต่อทีมงานได้เลยค่ะ"
                );
            }
            continue;
        }

        if (in_array(mb_strtolower($text), ['help', 'ช่วย', 'วิธี', 'help me'], true)) {
            lineReplyMessage(
                $replyToken,
                "วิธีเชื่อมบัญชี Wenxin Chinese\n"
                . "1. สมัคร/เข้าสู่ระบบที่เว็บ\n"
                . "2. ส่งเบอร์โทรที่ใช้สมัครในแชทนี้\n"
                . "3. รอข้อความยืนยัน ✅"
            );
            continue;
        }

        lineReplyMessage(
            $replyToken,
            "กรุณาส่งเฉพาะเบอร์โทรที่ใช้สมัครเรียน (เช่น 0812345678)\n"
            . "หรือพิมพ์「ช่วย」เพื่อดูวิธีเชื่อมบัญชี"
        );
    }
}

http_response_code(200);
echo 'OK';
