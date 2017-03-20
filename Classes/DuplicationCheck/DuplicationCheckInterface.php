<?php
namespace AawTeam\Minipoll\DuplicationCheck;

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

use AawTeam\Minipoll\Domain\Model\Participation;
use AawTeam\Minipoll\Domain\Model\Poll;

/**
 * DuplicationCheckInterface
 */
interface DuplicationCheckInterface
{
    /**
     * @param Poll $poll
     * @return bool
     */
    public function canVote(Poll $poll);

    /**
     * @param Poll $poll
     * @return bool
     */
    public function disableVote(Poll $poll, Participation $participation);
}
