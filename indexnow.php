<?php
set_time_limit(0); 
$apiKey = 'accc05987bcb4e9ca0b89cdf9dba181e';
$urlsFile = 'sitemap-urls-yourquorum-11.txt';  

// IndexNow endpoints
$endpoints = [
    'IndexNow' => 'https://api.indexnow.org/indexnow?url=',
    'Bing' => 'https://www.bing.com/indexnow?url=',
    'Naver' => 'https://searchadvisor.naver.com/indexnow?url=',
    'Seznam.cz' => 'https://search.seznam.cz/indexnow?url=',
    'Yandex' => 'https://yandex.com/indexnow?url=',
    'Yep' => 'https://indexnow.yep.com/indexnow?url='
];

// Read URLs from the file
$urls = file($urlsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Validate URLs
if ($urls === false) {
    die("Failed to read URLs from file.\n");
}

if (empty($urls)) {
    die("No URLs found in the file.\n");
}

// Initialize counts for each endpoint
$endpointSuccessCounts = array_fill_keys(array_keys($endpoints), 0);
$endpointFailureCounts = array_fill_keys(array_keys($endpoints), 0);

// Function to notify all endpoints
function notifyEndpoints($url, $apiKey, $endpoints) {
    global $endpointSuccessCounts, $endpointFailureCounts;

    foreach ($endpoints as $name => $endpoint) {
        $fullUrl = $endpoint . urlencode($url) . '&key=' . urlencode($apiKey);

        // Initialize cURL handle
        $ch = curl_init($fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL handle
        curl_close($ch);

        // Output HTTP status code and response for debugging
        echo "<pre>";
        echo "Endpoint: $name\n";
        echo "URL: $fullUrl\n";
        echo "HTTP Status Code: " . $httpCode . "\n";
        echo "Response: " . $response . "\n";

        // Count successful and failed requests for each endpoint
        if ($httpCode === 200) {
            $endpointSuccessCounts[$name]++;
        } else {
            $endpointFailureCounts[$name]++;
        }
    }
}

// Iterate over each URL and notify all endpoints
foreach ($urls as $url) {
    notifyEndpoints($url, $apiKey, $endpoints);
}

// Output overall counts and endpoint-specific counts
echo "Overall Successful requests: " . array_sum($endpointSuccessCounts) . "\n";
echo "Overall Failed requests: " . array_sum($endpointFailureCounts) . "\n";

foreach ($endpoints as $name => $endpoint) {
    echo "Endpoint: $name\n";
    echo "Successful requests: " . $endpointSuccessCounts[$name] . "\n";
    echo "Failed requests: " . $endpointFailureCounts[$name] . "\n";
    echo "<br><br>";
}

?>
