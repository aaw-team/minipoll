<?php
namespace AawTeam\Minipoll\Utility;

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

use AawTeam\Minipoll\Domain\Model\Poll;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FormProtectionUtility
 */
class FormProtectionUtility
{
    /**
     * @param Poll $poll
     * @see \TYPO3\CMS\Core\FormProtection\AbstractFormProtection::generateToken()
     * @return string
     */
    public function generateTokenForPoll(Poll $poll)
    {
        if ($this->isFormProtectionNeeded()) {
            return $this->getFormProtectionInstance()->generateToken('pollForm', 'vote', $poll->getUid());
        } else {
            return GeneralUtility::hmac(SecurityUtility::generateRandomBytes(16));
        }
    }

    /**
     * @param string $token
     * @param Poll $poll
     * @see \TYPO3\CMS\Core\FormProtection\AbstractFormProtection::validateToken()
     * @return boolean
     */
    public function verifyTokenForPoll($token, Poll $poll)
    {
        return $this->isFormProtectionNeeded()
            ? $this->getFormProtectionInstance()->validateToken($token, 'pollForm', 'vote', $poll->getUid())
            : true;
    }

    /**
     * @return void
     * @see \TYPO3\CMS\Core\FormProtection\AbstractFormProtection::persistSessionToken()
     */
    public function persistSessionToken()
    {
        if ($this->isFormProtectionNeeded()) {
            $this->getFormProtectionInstance()->persistSessionToken();
        }
    }

    /**
     * @return void
     * @see \TYPO3\CMS\Core\FormProtection\AbstractFormProtection::clean()
     */
    public function clean()
    {
        if ($this->isFormProtectionNeeded()) {
            $this->getFormProtectionInstance()->clean();
        }
    }

    /**
     * @return boolean
     */
    protected function isFormProtectionNeeded()
    {
        $formProtection = $this->getFormProtectionInstance();
        return ($formProtection instanceof \TYPO3\CMS\Core\FormProtection\FrontendFormProtection);
    }

    /**
     * @return \TYPO3\CMS\Core\FormProtection\AbstractFormProtection
     */
    protected function getFormProtectionInstance()
    {
        return \TYPO3\CMS\Core\FormProtection\FormProtectionFactory::get();
    }
}
