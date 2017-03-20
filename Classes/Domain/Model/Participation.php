<?php
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

/**
 * Participation model
 */
class Participation extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var \AawTeam\Minipoll\Domain\Model\Poll
     */
    protected $poll;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $frontendUser;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<AawTeam\Minipoll\Domain\Model\Answer>
     */
    protected $answers;

    /**
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     */
    public function setPoll(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * @return \AawTeam\Minipoll\Domain\Model\Poll
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
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser
     */
    public function setFrontendUser(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser)
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getFrontendUser()
    {
        return $this->frontendUser;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $options
     */
    public function setAnswers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $answers)
    {
        $this->answers = $answers;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<AawTeam\Minipoll\Domain\Model\Answer>
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param \AawTeam\Minipoll\Domain\Model\Answer $answers
     */
    public function addAnswer(Answer $answers)
    {
        $this->answers->attach($answers);
    }

    /**
     * @param \AawTeam\Minipoll\Domain\Model\Answer $answers
     */
    public function removeAnswer(Answer $answers)
    {
        $this->answers->detach($answers);
    }
}
