<?php
    $ch = curl_init();
    $base_url = "https://api.talentiq.co/v3/person?";
    $data = array(
        'name' => 'sean thorne',
        'profile' => 'www.linkedin.com/in/seanthorne',
        'api_key' => 'YOUR_API_KEY',
        'required' => 'emails.address'
    );
    $request_url = $base_url . http_build_query($data);
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $request_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    print_r($data);
    curl_close($ch);
?>
