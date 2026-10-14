<?php

if (!function_exists('srp_wa_preview')) {

    function srp_wa_bodies()
    {
        static $bodies = null;

        if ($bodies === null) {
            $bodies = include __DIR__ . '/../../../../meta-whatsapp/WhatsAppEngine/create_templates/betasms_vms_payloads.php';
        }

        return $bodies;
    }

    /** BetaSMS: body + footer from our own payload file. */
    function srp_betasms_template($template_name)
    {
        foreach (srp_wa_bodies() as $template) {
            if ($template['name'] == $template_name) {
                return array(
                    'body'   => $template['body'],
                    'footer' => isset($template['footer']) ? $template['footer'] : '',
                );
            }
        }

        return null;
    }

    function srp_superflo_cache_file($template_name)
    {
        return __DIR__ . '/../cache/superflo_' . preg_replace('/[^a-z0-9_]/', '', strtolower($template_name)) . '.json';
    }

    function srp_superflo_fetch($template_name)
    {
        $config = include __DIR__ . '/../../../../meta-whatsapp/WhatsAppEngine/wa_engine_config.php';
        $driver = $config['drivers']['superflo'];

        $curl = curl_init($driver['base_url'] . '/api/v1/public/templates/' . rawurlencode($template_name));
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => array('X-API-Key: ' . $driver['api_key']),
        ));
        $reply = json_decode(curl_exec($curl), true);
        curl_close($curl);

        if (empty($reply['success']) || empty($reply['body_text'])) {
            return null;
        }

        $footer = '';
        foreach ($reply['components'] as $component) {
            if ($component['type'] == 'FOOTER') {
                $footer = $component['text'];
            }
        }

        return array('body' => $reply['body_text'], 'footer' => $footer);
    }

    /** Superflo: body + footer fetched once from its API, then kept in cache/. */
    function srp_superflo_template($template_name)
    {
        $cache_file = srp_superflo_cache_file($template_name);

        if (file_exists($cache_file)) {
            return json_decode(file_get_contents($cache_file), true);
        }

        $template = srp_superflo_fetch($template_name);

        if ($template) {
            @mkdir(dirname($cache_file), 0755, true);
            @file_put_contents($cache_file, json_encode($template));
        }

        return $template;
    }

    function srp_wa_fill($body, $variables)
    {
        foreach ($variables as $index => $value) {
            $body = str_replace('{{' . ($index + 1) . '}}', $value, $body);
        }

        return $body;
    }

    /**
     * BetaSMS and Superflo rows. null when there is nothing to show.
     * Returns text, footer and image_url ('' when none).
     */
    function srp_wa_preview($message)
    {
        $payload = json_decode($message['request_payload'], true);

        if (!is_array($payload)) {
            return null;
        }

        if ($message['wa_driver'] == 'betasms_api') {
            $template  = srp_betasms_template($message['template_name']);
            $variables = isset($payload['variables']) ? $payload['variables'] : array();
            $image_url = isset($payload['header_media']['link']) ? $payload['header_media']['link'] : '';
        } else if ($message['wa_driver'] == 'superflo') {
            $template  = srp_superflo_template($message['template_name']);
            $variables = isset($payload['body_values']) ? $payload['body_values'] : array();
            $image_url = isset($payload['header_media_url']) ? $payload['header_media_url'] : '';
        } else {
            return null;
        }

        if (!$template) {
            return null;
        }

        return array(
            'text'      => srp_wa_fill($template['body'], $variables),
            'footer'    => $template['footer'],
            'image_url' => $image_url,
        );
    }
}
