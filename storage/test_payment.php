<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/cart.php';
require_once dirname(__DIR__) . '/includes/checkout_flow.php';

session_start();

$course = db()->query('SELECT id, title, price FROM courses WHERE is_active = 1 LIMIT 1')->fetch();
if (!$course) {
    echo "No course\n";
    exit(1);
}

$_SESSION['cart'] = [[
    'id' => (int) $course['id'],
    'title' => $course['title'],
    'price' => (float) $course['price'],
]];

try {
    $pdo = db();
    $pdo->beginTransaction();
    $paymentId = insertBankTransferPayment(
        (int) $course['id'],
        'ทดสอบชำระเงิน',
        'test@example.com',
        '0890000000',
        (float) $course['price'],
        date('Y-m-d'),
        '14:30',
        null,
        'cart_ids:' . (int) $course['id'],
        null
    );
    savePaymentItems($paymentId, cartItems());
    $pdo->rollBack();
    echo "OK payment insert test passed (rolled back), id would be {$paymentId}\n";
} catch (Throwable $e) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    echo 'FAIL: ' . $e->getMessage() . "\n";
    exit(1);
}
