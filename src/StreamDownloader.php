<?php

namespace Pdustdar\StreamDownloader;

class StreamDownloader
{
    public static function getHeaders($url)
    {
        $resualt = [];
        $headers = get_headers($url);
        foreach ($headers as $header) {
            $header_name = substr($header, 0, strpos($header, ":"));
            $header_value = substr($header, strpos($header, ":") + 2);
            if ($header_name != null && $header_name != "") {
                $resualt[$header_name] = $header_value;
            }
        }
        return $resualt;
    }
    public static function downloadToRam($url, $method = "GET", $headers = [], $part_size = (1024 * 1024))
    {
        $content = '';
        $header = '';
        foreach ($headers as $key => $value)
            $header += "$key: $value\r\n";
        set_time_limit(0);
        $content_length = self::getHeaders($url)['Content-Length'];
        $part_count = $content_length / $part_size;
        for ($i = 0; $i < $part_count; $i++) {
            $part_start = ($i * $part_size);
            $part_end = (($i + 1) * $part_size);
            $context = stream_context_create([
                'http' => [
                    'method' => $method,
                    'header' => $header + "Range: bytes=$part_start-" . ($part_end - 1) . "\r\n"
                ]
            ]);
            $content .= file_get_contents($url, false, $context);
        }
        return $content;
    }

    public static function downloadToDisk($url, $file_name = null, $method = "GET", $headers = [], $part_size = (1024 * 1024))
    {
        set_time_limit(0);
        $header = '';
        foreach ($headers as $key => $value)
            $header += "$key: $value\r\n";

        $content_length = self::getHeaders($url)['Content-Length'];
        $part_count = $content_length / $part_size;
        $fp = fopen($file_name, "a+");
        for ($i = 0; $i < $part_count; $i++) {
            $part_start = ($i * $part_size);
            $part_end = (($i + 1) * $part_size);
            $context = stream_context_create([
                'http' => [
                    'method' =>  $method,
                    'header' => $header . "Range: bytes=$part_start-" . ($part_end - 1) . "\r\n"
                ]
            ]);
            fwrite($fp, file_get_contents($url, false, $context));
        }
        fclose($fp);
        return $file_name;
    }
}
