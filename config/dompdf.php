<?php

return [
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,
    'options' => [
        "font_dir" => storage_path('fonts'),
        "font_cache" => storage_path('fonts'),
        "temp_dir" => sys_get_temp_dir(),
        "chroot" => realpath(base_path()),
        "enable_font_subsetting" => false,
        "pdf_backend" => "CPDF",
        "default_media_type" => "screen",
        "default_paper_size" => "a4",
        "default_font" => "serif",
        "shell_command" => "timeout 30s procopen",
        "isHtml5ParserEnabled" => true,
        "isRemoteEnabled" => true,
        "isPhpEnabled" => false,
        "isFontSubsettingEnabled" => false,
        "isJavascriptEnabled" => true,
    ],
];
