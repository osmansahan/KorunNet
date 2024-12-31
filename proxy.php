<?php
// Gelen e-posta adresini alın
if (!isset($_GET['email']) || empty($_GET['email'])) {
    echo json_encode(['success' => false, 'message' => 'Lütfen bir e-posta adresi girin.']);
    exit;
}

// Gelen e-posta adresini al
$email = $_GET['email'];

// LeakCheck API URL'si
$apiUrl = "https://leakcheck.io/api/public?check=" . urlencode($email);

try {
    // API'ye istek gönder
    $response = file_get_contents($apiUrl);

    // Yanıtı JSON olarak çöz
    $data = json_decode($response, true);

    // API'den dönen yanıtı kontrol et
    if (isset($data['success']) && $data['success']) {
        if (isset($data['found']) && $data['found'] > 0) {
            // Veri ihlalleri varsa döndür
            echo json_encode([
                'success' => true,
                'found' => $data['found'],
                'sources' => $data['sources']
            ]);
        } else {
            // Veri ihlali yoksa güvenli mesajı döndür
            echo json_encode([
                'success' => true,
                'found' => 0,
                'message' => 'E-posta adresiniz güvenli görünüyor.'
            ]);
        }
    } else {
        // API'den hata dönerse
        echo json_encode([
            'success' => false,
            'message' => isset($data['message']) ? $data['message'] : 'Girdiğiniz maile ait bir sızıntı bulunamadı.'
        ]);
    }
} catch (Exception $e) {
    // Hata durumunda hata mesajı döndür
    echo json_encode(['success' => false, 'message' => 'API çağrısı başarısız: ' . $e->getMessage()]);
}
?>