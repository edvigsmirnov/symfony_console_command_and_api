<?php

namespace App\Services;

use Exception;
use SimpleXMLElement;

class XmlReader
{
    /**
     * @var SimpleXMLElement
     */
    protected SimpleXMLElement $xml;

    /**
     * @var
     */
    protected  $xmlFilteredObjects;

    /**
     * @var int
     */
    protected int $currentPointer = 0;

    /**
     * @var
     */
    protected  $currentElement;

    /**
     * @throws Exception
     */
    public function __construct(?string $fileUrl)
    {
        $this->xml = simplexml_load_file($fileUrl);
    }

    /**
     * @param string $nodeName
     * @return static
     */
    public function filterXmlByNodeAndChild(string $nodeName, string $childName): static
    {
        $this->xmlFilteredObjects = $this->xml->{$nodeName}->{$childName};

        $this->currentElement = $this->xmlFilteredObjects[$this->currentPointer];

        return $this;
    }

    /**
     * @return ?SimpleXMLElement
     */
    public function next(): ?SimpleXMLElement
    {
        $this->currentPointer++;

        $this->currentElement = $this->xmlFilteredObjects[$this->currentPointer];

        return $this->currentElement;
    }

    /**
     * @return
     */
    public function current()
    {
        return $this->currentElement;
    }

    public function getFiltered()
    {
        return $this->xmlFilteredObjects;
    }

}