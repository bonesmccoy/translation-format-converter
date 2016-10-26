<?php

namespace Bones\TranslationFormatConverter\Model;

class XliffInputFile
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $resource;


    protected $acceptedExtension = ['xliff', 'xlf'];

    /**
     * XliffInputFile constructor.
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        $filename =  basename($resource);
        $pathInfo = pathinfo($filename);

        if (!in_array(strtolower($pathInfo['extension']), $this->acceptedExtension)) {
            throw new \InvalidArgumentException('Wrong file type '.$resource);
        }

        list($domain, $locale) = explode('.', basename($resource, ".xliff"));

        $this->domain = $domain;
        $this->locale = $locale;
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }
}
