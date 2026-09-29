<?php

return [
    'binaries' => [
        'ffmpeg' => env('FFMPEG_BINARY', 'ffmpeg'),
        'ffprobe' => env('FFPROBE_BINARY', 'ffprobe'),
    ],
    'timeout' => env('FFMPEG_TIMEOUT', 3600),
    'threads' => env('FFMPEG_THREADS', 4),
];