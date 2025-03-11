<?php
$config = json_decode(file_get_contents(__DIR__ . '/../config/config.json'), true);

$startTime = microtime(true);

$apiKey = filter_var($config['apiKey'], FILTER_SANITIZE_STRING);
$feedUrl = filter_var($config['feedUrl'], FILTER_SANITIZE_URL);
$orguserList = array_map(function($user) {
    return filter_var($user, FILTER_SANITIZE_STRING);
}, $config['org_list']);
$domain = filter_var($config['domain'], FILTER_SANITIZE_STRING);
$page = filter_var($config['page'], FILTER_VALIDATE_INT);
$limit = filter_var($config['limit'], FILTER_VALIDATE_INT);
$numPages = filter_var($config['numPages'], FILTER_VALIDATE_INT);

foreach ($config as $key => $value) {
    if ($key !== 'apiKey' && $key !== 'org_list') {
        error_log("$key: " . json_encode($value));
    }
}

$feed = [];
$multiHandle = curl_multi_init();
$curlHandles = [];

for ($i = 1; $i <= $numPages; $i++) {
    $requestData = json_encode([
        'page' => $i,
        'limit' => $limit,
    ]);

    $ch = curl_init($feedUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "apiKey: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
    curl_multi_add_handle($multiHandle, $ch);
    $curlHandles[$i] = $ch;
}

$running = null;
do {
    curl_multi_exec($multiHandle, $running);
    curl_multi_select($multiHandle);
} while ($running > 0);

foreach ($curlHandles as $i => $ch) {
    $response = curl_multi_getcontent($ch);
    $data = json_decode($response, true);

    if ($data && isset($data['success']) && $data['success']) {
        $feed = array_merge($feed, $data['recognitions'] ?? []);
    } else {
        $error = $data['message'] ?? 'Unknown error';
        error_log('Error fetching page ' . $i . ': ' . $error);
    }

    curl_multi_remove_handle($multiHandle, $ch);
    curl_close($ch);
}

curl_multi_close($multiHandle);

function filterFeedByUsername($feed, $orguserList, $domain) {
    $emailList = array_map(function($username) use ($domain) {
        return filter_var($username, FILTER_SANITIZE_STRING) . '@' . filter_var($domain, FILTER_SANITIZE_STRING);
    }, $orguserList);

    return array_filter($feed, function($item) use ($emailList) {
        return in_array($item['to'][0]['email'] ?? '', $emailList);
    });
}

function filterOutSolrAdminInfoSystem($feed) {
    return array_filter($feed, function($item) {
        return strpos($item['request_uri'] ?? '', 'solr/admin/info/system') === false;
    });
}

error_log('Total actual recognitions: ' . count($feed));
$feed = filterFeedByUsername($feed, $orguserList, $domain);
$feed = filterOutSolrAdminInfoSystem($feed);
error_log('Total filtered recognitions: ' . count($feed));

$endTime = microtime(true);
$timeConsumed = $endTime - $startTime;
error_log('Time consumed to load the screen: ' . $timeConsumed . ' seconds');
?>