<?php

namespace Bones\TranslationFormatConverter\Model;

class YamlOutputFile
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $content;

    /**
     * YamlOutput constructor.
     * @param string $fileName
     * @param string $content
     */
    public function __construct($fileName, $content)
    {
        $this->fileName = $fileName;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
