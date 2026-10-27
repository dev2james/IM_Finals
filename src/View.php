<?php
final class View
{
    public static function render(string $page, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . "/../views/$page.php";
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }
}
