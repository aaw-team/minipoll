<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\Domain\Model;

/*
 * Copyright 2017 Agentur am Wasser | Maeder & Partner AG
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Poll model
 */
class Poll extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    const STATUS_CLOSED = 0;
    const STATUS_OPEN = 1;
    const STATUS_BYDATE = 2;

    const DISPLAY_RESULTS_ALWAYS = 0;
    const DISPLAY_RESULTS_ONVOTE = 1;
    const DISPLAY_RESULTS_NEVER = 2;

    const DUPLICATION_CHECK_IP = 'ip';
    const DUPLICATION_CHECK_COOKIE = 'cookie';
    const DUPLICATION_CHECK_FEUSER = 'feuser';
    const DUPLICATION_CHECK_FRONTEND_SESSION = 'frontend-session';
    const DUPLICATION_CHECK_NONE = 'none';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $useCaptcha;

    /**
     * @var string
     */
    protected $duplicationCheck;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $openDatetime;

    /**
     * @var \DateTime
     */
    protected $closeDatetime;

    /**
     * @var bool
     */
    protected $allowMultiple;

    /**
     * @var int
     */
    protected $displayResults;

    /**
     * @var ObjectStorage<PollOption>
     */
    protected $options;

    /**
     * @var ObjectStorage<Participation>
     */
    protected $participations;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param boolean $useCaptcha
     */
    public function setUseCaptcha($useCaptcha)
    {
        $this->useCaptcha = $useCaptcha;
    }

    /**
     * @return boolean
     */
    public function getUseCaptcha()
    {
        return $this->useCaptcha;
    }

    /**
     * @param string $duplicationCheck
     */
    public function setDuplicationCheck($duplicationCheck)
    {
        $this->duplicationCheck = $duplicationCheck;
    }

    /**
     * @return string
     */
    public function getDuplicationCheck()
    {
        return $this->duplicationCheck;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \DateTime $openDatetime
     */
    public function setOpenDatetime(\DateTime $openDatetime)
    {
        $this->openDatetime = $openDatetime;
    }

    /**
     * @return \DateTime
     */
    public function getOpenDatetime()
    {
        return $this->openDatetime;
    }

    /**
     * @param \DateTime $closeDatetime
     */
    public function setCloseDatetime(\DateTime $closeDatetime)
    {
        $this->closeDatetime = $closeDatetime;
    }

    /**
     * @return \DateTime
     */
    public function getCloseDatetime()
    {
        return $this->closeDatetime;
    }

    /**
     * @param bool $allowMultiple
     */
    public function setAllowMultiple($allowMultiple)
    {
        $this->allowMultiple = $allowMultiple;
    }

    /**
     * @return boolean
     */
    public function getAllowMultiple()
    {
        return $this->allowMultiple;
    }

    /**
     * @param int $displayResults
     */
    public function setDisplayResults($displayResults)
    {
        $this->displayResults = $displayResults;
    }

    /**
     * @return int
     */
    public function getDisplayResults()
    {
        return $this->displayResults;
    }

    /**
     * @param ObjectStorage $options
     */
    public function setOptions(ObjectStorage $options)
    {
        $this->options = $options;
    }

    /**
     * @return ObjectStorage<PollOption>
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param PollOption $option
     */
    public function addOption(PollOption $option)
    {
        $this->options->attach($option);
    }

    /**
     * @param PollOption $option
     */
    public function removeOption(PollOption $option)
    {
        $this->options->detach($option);
    }

    /**
     * @param ObjectStorage $participations
     */
    public function setParticipations(ObjectStorage $participations)
    {
        $this->participations = $participations;
    }

    /**
     * @return ObjectStorage<Participation>
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @param Participation $participation
     */
    public function addParticipation(Participation $participation)
    {
        $this->participations->attach($participation);
    }

    /**
     * @param Participation $participation
     */
    public function removeParticipation(Participation $participation)
    {
        $this->participations->detach($participation);
    }

    /* Business logic stuff */

    /**
     * @return boolean
     */
    public function getIsOpen()
    {
        if ($this->status === static::STATUS_BYDATE) {
            return ($this->openDatetime === null || $this->openDatetime->getTimestamp() <= $GLOBALS['EXEC_TIME'])
                && ($this->closeDatetime === null || $this->closeDatetime->getTimestamp() >= $GLOBALS['EXEC_TIME']);
        }
        return $this->status === static::STATUS_OPEN;
    }

    /**
     * @return boolean
     */
    public function getIsClosed()
    {
        return !$this->getIsOpen();
    }
}
