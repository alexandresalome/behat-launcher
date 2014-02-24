<?php

namespace Alex\BehatLauncher\Behat;

class OutputFile
{
    /**
     * Path to output file.
     *
     * @var string
     */
    private $path;

    /**
     * Storage ID.
     *
     * @var string
     */
    private $id;

    /**
     * Constructs the output file.
     *
     * If no path is given, a temporary file will be created on first write.
     *
     * @param string $path an absolute path
     */
    public function __construct($path = null, $id = null)
    {
        if (null === $path) {
            $path = tempnam(sys_get_temp_dir(), 'bl_');
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->path = $path;
        $this->id   = $id;
    }

    /**
     * Deletes the file.
     *
     * @return OutputFile
     */
    public function delete()
    {
        if ($this->path !== null && file_exists($this->path)) {
            unlink($this->path);
        }

        return $this;
    }

    /**
     * Moves the output file to a new location.
     *
     * @param string $path an absolute path
     *
     * @return OutputFile
     */
    public function moveTo($path)
    {
        if (!$this->exists()) {
            throw new \LogicException(sprintf('Cannot move output file to %s, file does not exist (path: %s).', $path, $this->path ?: '*none*'));
        }

        $before = $this->path;
        $this->path = $path;
        rename($before, $this->getOrCreatePath());

        return $this;
    }

    /**
     * Appends a content to the file.
     *
     * @return OutputFile
     */
    public function append($content)
    {
        $fp = fopen($this->getOrCreatePath(), 'a+');
        fwrite($fp, $content);
        fclose($fp);

        return $this;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        $finfo = new \finfo(FILEINFO_MIME);

        if (!$this->exists()) {
            throw new \LogicException(sprintf('Path "%s" does not exist.', $this->path ?: '*none*'));
        }

        return $finfo->file($this->path);
    }

    /**
     * Changes the file content.
     *
     * @param string $content new content to set
     *
     * @return OutputFile
     */
    public function setContent($content)
    {
        file_put_contents($this->getOrCreatePath(), $content);

        return $this;
    }

    /**
     * Tests if file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->path !== null && file_exists($this->path);
    }

    public function isEmpty()
    {
        return !$this->exists() || filesize($this->path) == 0;
    }


    /**
     * @return string an absolute path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return OutputFile
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return OutputFile
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * Prepares path for writing.
     *
     * @return string an absolute path
     */
    private function getOrCreatePath()
    {
        if (null !== $this->path) {
            $path = $this->path;
        } else {
            $path = tempnam(sys_get_temp_dir(), 'bl_');
        }

        if (!is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }

        return $path;
    }
}
