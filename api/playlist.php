<?php

// Set the content type to application/vnd.apple.mpegurl for m3u8 file
header('Content-Type: application/vnd.apple.mpegurl');
// Suggest a filename for the playlist
header('Content-Disposition: inline; filename="playlist.m3u8"');

// URL of the API endpoint
$url = 'https://babel-in.xyz/babel-09881c7dca3a37e7abb447153831e9f5/fancode';

// Initialize a cURL session
$ch = curl_init();

// Set the URL to fetch
curl_setopt($ch, CURLOPT_URL, $url);

// Set cURL option to return the response as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($ch);

// Check if the request was successful
if ($response === false) {
    // Handle error
    echo '#EXTM3U' . PHP_EOL;
    echo '#EXTINF:-1,Error fetching data' . PHP_EOL;
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the data was decoded correctly
    if (json_last_error() === JSON_ERROR_NONE) {
        // Start the M3U playlist
        echo '#EXTM3U' . PHP_EOL;

        // Check if there is at least one item to get the tournament name
        if (!empty($data['data'])) {
            // Capitalize and include tournament details at the top of the playlist
            $tournament_name = ucwords(strtolower($data['data'][0]['tournament']));
            echo '#EXTINF:-1,' . htmlspecialchars($tournament_name) . PHP_EOL;
        }

        // Iterate through the data and create the M3U8 entries with custom EPG info
        foreach ($data['data'] as $item) {
            // Capitalize tvg-name and group-title (tournament name)
            $tvg_id = htmlspecialchars($item['id']); // Unique ID for the stream
            $tvg_name = ucwords(strtolower(htmlspecialchars($item['title']))); // Capitalize first letter of each word
            $tvg_logo = htmlspecialchars($item['poster']); // Logo image
            $group_title = ucwords(strtolower(htmlspecialchars($item['tournament']))); // Capitalize first letter of each word

            // Create the #EXTINF line with capitalized names
            echo '#EXTINF:-1 tvg-id="' . $tvg_id . '" tvg-name="' . $tvg_name . '" tvg-logo="' . $tvg_logo . '" group-title="' . $group_title . '",' . $tvg_name . PHP_EOL;
            echo htmlspecialchars($item['initialUrl']) . PHP_EOL;
        }
    } else {
        echo '#EXTM3U' . PHP_EOL;
        echo '#EXTINF:-1,Error decoding JSON' . PHP_EOL;
    }
}

// Close the cURL session
curl_close($ch);

?>
