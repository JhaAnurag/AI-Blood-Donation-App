<?php
// AJAX handler for the floating chatbot
require_once 'config.php';
require_once 'gemini_handler.php';
require_once dirname(__DIR__) . '/includes/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set response header to JSON
header('Content-Type: application/json');

// Check for POST request with message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $user_message = trim($_POST['message']);
    
    if (empty($user_message)) {
        echo json_encode(['success' => false, 'response' => 'Please enter a message.']);
        exit;
    }
    
    // Define common FAQs for blood donation - Same as in index.php
    $faqs = [
        'Who can donate blood?' => 'Generally, donors must be at least 17 years old, weigh at least 110 pounds, and be in good health.',
        'How often can I donate?' => 'Whole blood donation can be done every 56 days (8 weeks).',
        'What blood types are most needed?' => 'All blood types are needed, but O negative is the universal donor type and always in high demand. AB positive is the universal recipient type.',
        'How long does donation take?' => 'The actual blood donation takes only 8-10 minutes, but the entire process including registration, mini-physical, and refreshments takes about an hour.',
        'Does blood donation hurt?' => 'Most people feel only a brief sting from the needle insertion. The donation process itself is typically painless.',
        'How much blood is taken?' => 'A typical whole blood donation is approximately one pint (about 470 ml).',
        'Are there any side effects?' => 'Some donors might experience mild side effects like lightheadedness, dizziness, or bruising at the needle site.',
        'What happens to my blood after donation?' => 'Your blood is tested, processed, and separated into components (red cells, platelets, plasma), then distributed to hospitals.',
        'Can I donate if I have a cold?' => 'No, you should be feeling well on the day of donation.',
        'Is it safe to donate during COVID-19?' => 'Yes, blood donation centers have implemented enhanced safety protocols to protect donors and staff.',
        'Can I donate if I have high blood pressure?' => 'You may be eligible if your blood pressure is within acceptable limits at the time of donation.',
        'Can I donate if I have diabetes?' => 'Yes, if your diabetes is well-controlled and you feel healthy.',
        'Can I donate if I have tattoos or piercings?' => 'Policies vary by location, but generally if the tattoo was done in a licensed facility and is fully healed, you can donate.',
        'What should I eat before donating blood?' => 'Have a healthy meal and drink plenty of fluids before donating.',
        'Will donating blood affect my athletic performance?' => 'It may temporarily impact intense exercise performance, so it\'s best to donate on a rest day.',
        'How quickly does my body replace donated blood?' => 'Your body replaces plasma within 24 hours. Red blood cells are replaced within 4-6 weeks.',
        'What is the difference between whole blood donation and platelet donation?' => 'Whole blood donation collects all blood components, while platelet donation specifically collects platelets through a process called apheresis.',
        'Can I donate if I\'ve recently traveled abroad?' => 'Travel to certain countries may temporarily defer donation due to risk of infections like malaria.',
        'What medications prevent blood donation?' => 'Blood thinners, certain acne medications like isotretinoin, and some other prescriptions may prevent donation.',
        'How will I feel after donating?' => 'Most people feel fine after donating. It\'s recommended to have a snack, drink extra fluids, and avoid strenuous activity for the rest of the day.'
    ];
    
    // First check against FAQs
    $faq_response = matchFAQ($user_message, $faqs);
    
    if ($faq_response) {
        $response = $faq_response;
    } else {
        // If no FAQ match, use the new Gemini 2.0 API
        $response = getGeminiResponse($user_message);
    }
    
    // Store in floating chat history (separate from the main chat page history)
    if (!isset($_SESSION['floating_chat_history'])) {
        $_SESSION['floating_chat_history'] = [];
    }
    
    $_SESSION['floating_chat_history'][] = [
        'user' => $user_message,
        'bot' => $response
    ];
    
    // Return the response as JSON
    echo json_encode(['success' => true, 'response' => $response]);
    exit;
} else {
    // Invalid request
    echo json_encode(['success' => false, 'response' => 'Invalid request method or missing message.']);
    exit;
}
?>
