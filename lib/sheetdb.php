<?Php

function pushUsersTosheetdb($data) {
    $args = array(
        'body' => json_encode($data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    );
    
    $response = wp_remote_post('https://sheetdb.io/api/v1/API_KEY_HERE', $args);
    
    if (is_wp_error($response)) {
        // Handle error
        echo "Error: " . $response->get_error_message();
    } else {
        // Success
        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }
      }
function pushProductsTosheetdb($data) {
    $args = array(
        'body' => json_encode($data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    );
    
    $response = wp_remote_post('https://sheetdb.io/api/v1/API_KEY_HERE', $args);
    if (is_wp_error($response)) {
        // Handle error
        return "Error: " . $response->get_error_message();
    } else {
        // Success
        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }
      }