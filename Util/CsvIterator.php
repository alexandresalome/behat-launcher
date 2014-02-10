<?php

namespace Alex\BehatLauncherBundle\Util;

class CsvIterator
{
    /**
     * @var string
     */
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function search(array $filters = array(), $limit = 10)
    {
        if (!file_exists($this->path)) {
            return array();
        }

        $file = fopen($this->path, 'r');
        fseek($file, 0, SEEK_END);

        $result = array();
        while (count($result) < $limit && $line = $this->readLineFromFile($file)) {
            $actual = str_getcsv($line);
            foreach ($filters as $col => $val) {
                if (!isset($actual[$col]) || $actual[$col] !== $val) {
                    continue 2;
                }
            }

            $result[] = $actual;
        }

        fclose($file);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function addRow(array $values)
    {
        if (!is_dir($dir = dirname($this->path))) {
            mkdir($dir, 0777, true);
        }

        if (false === $file = fopen($this->path, 'a')) {
            throw new \RuntimeException(sprintf('Unable to write file "%s".', $this->path));
        }

        fputcsv($file, $values);
        fclose($file);

        return $this;
    }

    private function readLineFromFile($file)
    {
        $line = '';
        $position = ftell($file);

        if (0 === $position) {
            return null;
        }

        while (true) {
            $chunkSize = min($position, 1024);
            $position -= $chunkSize;
            fseek($file, $position);

            if (0 === $chunkSize) {
                // bof reached
                break;
            }

            $buffer = fread($file, $chunkSize);

            if (false === ($upTo = strrpos($buffer, "\n"))) {
                $line = $buffer.$line;
                continue;
            }

            $position += $upTo;
            $line = substr($buffer, $upTo + 1).$line;
            fseek($file, max(0, $position), SEEK_SET);

            if ('' !== $line) {
                break;
            }
        }

        return '' === $line ? null : $line;
    }
}
