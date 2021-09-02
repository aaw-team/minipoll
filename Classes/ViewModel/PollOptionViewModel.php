<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\ViewModel;

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

use AawTeam\Minipoll\Domain\Model\PollOption;

/**
 * PollOptionViewModel
 */
class PollOptionViewModel extends AbstractViewModel
{
    /**
     * @var \AawTeam\Minipoll\Domain\Model\PollOption
     */
    protected $domainModel;

    /**
     * @param PollOption $pollOption
     * @param array $options
     * @return \AawTeam\Minipoll\ViewModel\PollOptionViewModel
     */
    public static function createFromPollOption(PollOption $pollOption, array $options = null)
    {
        return parent::createFromDomainModel($pollOption, $options);
    }

    /**
     * @return \AawTeam\Minipoll\Domain\Model\PollOption
     */
    public function getPollOption()
    {
        return $this->domainModel;
    }
}
