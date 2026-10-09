<?php
namespace PCM\Security;

if (!defined('ABSPATH')) {
    exit;
}

final class NonceValidator
{
    public static function validate_rest_request(\WP_REST_Request $request): bool
    {
        $nonce = $request->get_header('X-WP-Nonce');

        if (empty($nonce)) {
            return false;
        }

        return wp_verify_nonce($nonce, 'wp_rest');
    }
}
