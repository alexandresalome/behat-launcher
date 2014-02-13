<?php

namespace Alex\BehatLauncher\Behat;

class OutputFile
{
    private $path;
    private $id;

    /**
     * @param string $path an absolute path
     * @param string $id   the storage ID
     */
    public function __construct($path = null, $id = null)
    {
        $this->path = $path;
        $this->id   = $id;
    }

    /**
     * @return string an absolute path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Changes file path.
     *
     * @param string $path an absolute path
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
     * @return string
     */
    public function getMimetype()
    {
        $finfo = new \finfo(FILEINFO_MIME);

        if (!file_exists($this->path)) {
            throw new \InvalidArgumentException(sprintf('Path "%s" does not exist.', $this->path));
        }

        return $finfo->file($this->path);
    }

    /**
     * Replaces with given file.
     *
     * @param string $path path to move from
     *
     * @return OutputFile
     */
    public function moveFrom($path)
    {
        if (!is_dir($dir = dirname($this->path))) {
            mkdir($dir, 0777, true);
        }

        rename($path, $this->path);

        return $this;
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
        if (!is_dir($dir = dirname($this->path))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($this->path, $content);

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (!file_exists($this->path)) {
            throw new \RuntimeException(sprintf('File "%s" is not readable.', $this->path));
        }

        return file_get_contents($this->path);
    }

    public function exists()
    {
        return file_exists($this->path);
    }

    public function isEmpty()
    {
        if (!file_exists($this->path)) {
            return true;
        }

        return filesize($this->path) == 0;
    }
}
