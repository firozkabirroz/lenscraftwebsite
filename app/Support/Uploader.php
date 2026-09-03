<?php

namespace App\Support;

use RuntimeException;

class Uploader
{
    /**
     * Store a normal (single request) upload and register it in the media table.
     *
     * @param array $file One entry of $_FILES
     */
    public static function storeImage(array $file, string $title = ''): array
    {
        self::assertOk($file);

        $cfg = config('uploads');
        $mime = self::mime($file['tmp_name'], $file['name']);

        if (!in_array($mime, array_merge($cfg['image_types'], $cfg['doc_types']), true)) {
            throw new RuntimeException('Unsupported file type: ' . $mime);
        }
        if ($file['size'] > $cfg['max_image']) {
            throw new RuntimeException('File is larger than ' . human_size($cfg['max_image']));
        }

        $isDoc = in_array($mime, $cfg['doc_types'], true);
        $folder = $isDoc ? 'images' : 'images';
        $name = self::uniqueName($file['name']);
        $target = rtrim($cfg['image_dir'], '/\\') . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('Could not save the uploaded file.');
        }

        [$width, $height] = self::dimensions($target, $mime);

        $id = Database::insert('media', [
            'title'      => $title !== '' ? $title : pathinfo($file['name'], PATHINFO_FILENAME),
            'filename'   => $name,
            'path'       => $folder . '/' . $name,
            'mime'       => $mime,
            'type'       => $isDoc ? 'doc' : 'image',
            'width'      => $width,
            'height'     => $height,
            'size_bytes' => (int) $file['size'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Activity::log('uploaded', 'media', $id, $name);

        return ['id' => $id, 'path' => $folder . '/' . $name, 'name' => $name, 'mime' => $mime];
    }

    /**
     * Append one chunk of a large video upload. Returns ['done' => bool, 'path' => ?string].
     */
    public static function receiveChunk(string $uploadId, int $index, int $total, string $originalName, array $file): array
    {
        self::assertOk($file);

        $cfg = config('uploads');
        $uploadId = preg_replace('/[^a-z0-9\-]/i', '', $uploadId) ?: '';

        if ($uploadId === '' || $total < 1 || $index < 0 || $index >= $total) {
            throw new RuntimeException('Invalid chunk request.');
        }

        $tempDir = rtrim($cfg['temp_dir'], '/\\') . DIRECTORY_SEPARATOR . $uploadId;
        if (!is_dir($tempDir) && !mkdir($tempDir, 0775, true) && !is_dir($tempDir)) {
            throw new RuntimeException('Could not create the temp folder.');
        }

        if (!move_uploaded_file($file['tmp_name'], $tempDir . DIRECTORY_SEPARATOR . $index . '.part')) {
            throw new RuntimeException('Could not save chunk ' . $index);
        }

        $received = count(glob($tempDir . DIRECTORY_SEPARATOR . '*.part') ?: []);
        if ($received < $total) {
            return ['done' => false, 'received' => $received, 'total' => $total];
        }

        $name = self::uniqueName($originalName);
        $target = rtrim($cfg['video_dir'], '/\\') . DIRECTORY_SEPARATOR . $name;
        $out = fopen($target, 'wb');

        if ($out === false) {
            throw new RuntimeException('Could not open the target file for writing.');
        }

        for ($i = 0; $i < $total; $i++) {
            $part = $tempDir . DIRECTORY_SEPARATOR . $i . '.part';
            $in = fopen($part, 'rb');
            if ($in === false) {
                fclose($out);
                throw new RuntimeException('Missing chunk ' . $i);
            }
            stream_copy_to_stream($in, $out);
            fclose($in);
            @unlink($part);
        }

        fclose($out);
        @rmdir($tempDir);

        $size = (int) filesize($target);
        if ($size > $cfg['max_video']) {
            @unlink($target);
            throw new RuntimeException('Video is larger than ' . human_size($cfg['max_video']));
        }

        return ['done' => true, 'path' => 'videos/' . $name, 'name' => $name, 'size' => $size];
    }

    public static function cleanupStaleTemp(int $olderThanHours = 24): void
    {
        $tempDir = rtrim(config('uploads')['temp_dir'], '/\\');
        foreach (glob($tempDir . DIRECTORY_SEPARATOR . '*') ?: [] as $dir) {
            if (is_dir($dir) && filemtime($dir) < time() - $olderThanHours * 3600) {
                foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $part) {
                    @unlink($part);
                }
                @rmdir($dir);
            }
        }
    }

    public static function deleteFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $base = dirname(config('uploads')['video_dir']);
        $full = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($full)) {
            @unlink($full);
        }
    }

    private static function assertOk(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid upload.');
        }

        match ($file['error']) {
            UPLOAD_ERR_OK => null,
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => throw new RuntimeException('File exceeds the PHP upload_max_filesize limit.'),
            UPLOAD_ERR_NO_FILE => throw new RuntimeException('No file was selected.'),
            UPLOAD_ERR_PARTIAL => throw new RuntimeException('The upload was interrupted.'),
            default => throw new RuntimeException('Upload failed (code ' . $file['error'] . ').'),
        };
    }

    private static function mime(string $tmpPath, string $originalName): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? (finfo_file($finfo, $tmpPath) ?: '') : '';
        if ($finfo) {
            finfo_close($finfo);
        }

        // SVG and some text formats are detected as text/plain or text/xml.
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === 'svg') {
            return 'image/svg+xml';
        }

        return $mime !== '' ? $mime : 'application/octet-stream';
    }

    private static function dimensions(string $path, string $mime): array
    {
        if (!str_starts_with($mime, 'image/') || $mime === 'image/svg+xml') {
            return [0, 0];
        }

        $info = @getimagesize($path);

        return $info ? [(int) $info[0], (int) $info[1]] : [0, 0];
    }

    private static function uniqueName(string $originalName): string
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $stem = slugify(pathinfo($originalName, PATHINFO_FILENAME));

        return substr($stem, 0, 60) . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(4)), 0, 6) . '.' . $ext;
    }
}
