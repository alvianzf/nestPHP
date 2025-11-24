<?php

namespace Framework\Core;

abstract class BaseController
{
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function html(string $html, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/html');
        echo $html;
    }

    // Optional: allow returning a value to let Router handle it
    protected function response($data)
    {
        // If array/object -> JSON, else string -> HTML
        if (is_array($data) || is_object($data)) {
            $this->json($data);
        } else {
            $this->html((string)$data);
        }
    }
}
