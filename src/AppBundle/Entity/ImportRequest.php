<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="import_request")
 */
class ImportRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $requestDateTime;

    /**
     * @ORM\Column(type="string")
     */
    private $file;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startTime;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endTime;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $successCount;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalProcessedCount;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $exceptions="";

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRequestDateTime()
    {
        return $this->requestDateTime;
    }

    /**
     * @param mixed $requestDateTime
     * @return ImportRequest
     */
    public function setRequestDateTime($requestDateTime)
    {
        $this->requestDateTime = $requestDateTime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return ImportRequest
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     * @return ImportRequest
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     * @return ImportRequest
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    /**
     * @param mixed $successCount
     * @return ImportRequest
     */
    public function setSuccessCount($successCount)
    {
        $this->successCount = $successCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalProcessedCount()
    {
        return $this->totalProcessedCount;
    }

    /**
     * @param mixed $totalProcessedCount
     * @return ImportRequest
     */
    public function setTotalProcessedCount($totalProcessedCount)
    {
        $this->totalProcessedCount = $totalProcessedCount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param mixed $exceptions
     * @return ImportRequest
     */
    public function setExceptions($exceptions)
    {
        $this->exceptions = $exceptions;

        return $this;
    }


}