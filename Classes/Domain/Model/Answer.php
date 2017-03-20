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
 * Answer model
 */
class Answer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var \AawTeam\Minipoll\Domain\Model\Participation
     */
    protected $participation;

    /**
     * @var \AawTeam\Minipoll\Domain\Model\PollOption
     */
    protected $pollOption;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param \AawTeam\Minipoll\Domain\Model\Participation $participation
     */
    public function setParticipation(Participation $participation)
    {
        $this->participation = $participation;
    }

    /**
     * @return \AawTeam\Minipoll\Domain\Model\Participation
     */
    public function getParticipation()
    {
        return $this->participation;
    }

    /**
     * @param \AawTeam\Minipoll\Domain\Model\PollOption $pollOption
     */
    public function setPollOption(PollOption $pollOption)
    {
        $this->pollOption = $pollOption;
    }

    /**
     * @return \AawTeam\Minipoll\Domain\Model\PollOption
     */
    public function getPollOption()
    {
        return $this->pollOption;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
