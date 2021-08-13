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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * PollOption model
 */
class PollOption extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var Poll
     */
    protected $poll;

    /**
     * @var ObjectStorage<Answer>
     */
    protected $answers;

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
     * @param Answer $answer
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers->attach($answer);
    }

    /**
     * @param Answer $answer
     */
    public function removeAnswer(Answer $answer)
    {
        $this->answers->detach($answer);
    }
}
