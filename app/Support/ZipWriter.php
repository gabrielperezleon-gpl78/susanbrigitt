<?php

namespace App\Support;

/** Write a basic, uncompressed ZIP archive without requiring the PHP ZIP extension. */
class ZipWriter
{
    private $file;
    private array $entries = [];

    public function __construct(string $path)
    {
        $this->file = fopen($path, 'wb');
        if ($this->file === false) {
            throw new \RuntimeException('No se pudo crear el archivo Excel.');
        }
    }

    public function add(string $name, string $content): void
    {
        $size = strlen($content);
        $crc = crc32($content);
        $offset = ftell($this->file);
        $header = pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $size, $size, strlen($name), 0);
        $this->write($header.$name.$content);
        $this->entries[] = compact('name', 'size', 'crc', 'offset');
    }

    public function close(): void
    {
        if ($this->file === null) {
            return;
        }

        try {
            $start = ftell($this->file);
            foreach ($this->entries as $entry) {
                $name = $entry['name'];
                $size = $entry['size'];
                $header = pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, 0, 0,
                    $entry['crc'], $size, $size, strlen($name), 0, 0, 0, 0, 0, $entry['offset']);
                $this->write($header.$name);
            }
            $end = ftell($this->file);
            $count = count($this->entries);
            $this->write(pack('VvvvvVVv', 0x06054b50, 0, 0, $count, $count, $end - $start, $start, 0));
        } finally {
            fclose($this->file);
            $this->file = null;
        }
    }

    private function write(string $bytes): void
    {
        $length = strlen($bytes);
        while ($length > 0) {
            $written = fwrite($this->file, $bytes);
            if ($written === false || $written === 0) {
                throw new \RuntimeException('No se pudo escribir el archivo Excel.');
            }
            $bytes = substr($bytes, $written);
            $length -= $written;
        }
    }
}
