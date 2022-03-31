<?php


// Verifies that the post type that's being saved is actually a post (versus a page or another custom post type

function is_valid_post_type() {
    return ! empty( $_POST['post_type'] ) && 'post' == $_POST['post_type'];
}

// Determines whether or not the current user has the ability to save meta data associated with this post.

function user_can_save( $post_id, $nonce_action, $nonce_id ) {
 
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ $nonce_action ] ) && wp_verify_nonce( $_POST[ $nonce_action ], $nonce_id ) );
 
    // Return true if the user is able to save; otherwise, false.
    return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;
 
}


//Return the state of the link
function url_status($href){

    $url = parse_url($href);

    if($url['scheme'] === 'http'){
        return 'Enlace inseguro';
    }

    if(empty($url['scheme'])){
        return 'Protocolo no especificado';
    }

    if(!filter_var($href, FILTER_VALIDATE_URL)){
        return 'Enlace malformado';
    }

    $status = get_http_code($href);

    if($status !== 200){
        return $status;
    }

    return null;
}

//Get link status code

function get_http_code($url) {
    $handle = curl_init($url);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($handle);
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);
    return http_response_code_text($httpCode);         
}

// We get the description of the status code
function http_response_code_text($code = NULL) {

    if ($code !== NULL) {

        if($code >= 200 && $code < 300) return 200;

        switch ($code) {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Moved Temporarily'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Time-out'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Request Entity Too Large'; break;
            case 414: $text = 'Request-URI Too Large'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Time-out'; break;
            case 505: $text = 'HTTP Version not supported'; break;
            default:
                return 'Unknown http status code "' . htmlentities($code) . '"';
            break;
        }


        return $code . ' ' . $text;

    }

}  