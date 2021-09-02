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

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Participation model
 */
class Participation extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var Poll
     */
    protected $poll;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var FrontendUser
     */
    protected $frontendUser;

    /**
     * @var ObjectStorage<Answer>
     */
    protected $answers;

    /**
     * @param Poll $poll
     */
    public function setPoll(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * @return Poll
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param FrontendUser $frontendUser
     */
    public function setFrontendUser(FrontendUser $frontendUser)
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * @return FrontendUser
     */
    public function getFrontendUser()
    {
        return $this->frontendUser;
    }

    /**
     * @param ObjectStorage $options
     */
    public function setAnswers(ObjectStorage $answers)
    {
        $this->answers = $answers;
    }

    /**
     * @return ObjectStorage<Answer>
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param Answer $answers
     */
    public function addAnswer(Answer $answers)
    {
        $this->answers->attach($answers);
    }

    /**
     * @param Answer $answers
     */
    public function removeAnswer(Answer $answers)
    {
        $this->answers->detach($answers);
    }
}
