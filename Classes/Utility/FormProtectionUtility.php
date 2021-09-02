<?php
declare(strict_types=1);
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
use TYPO3\CMS\Core\FormProtection\AbstractFormProtection;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FormProtectionUtility
 */
class FormProtectionUtility
{
    /**
     * @param Poll $poll
     * @see AbstractFormProtection::generateToken()
     * @return string
     */
    public function generateTokenForPoll(Poll $poll): string
    {
        if ($this->isFormProtectionNeeded()) {
            return $this->getFormProtectionInstance()->generateToken(...$this->createTokenGenerationParams($poll));
        } else {
            return GeneralUtility::hmac(SecurityUtility::generateRandomBytes(16));
        }
    }

    /**
     * @param string $token
     * @param Poll $poll
     * @see AbstractFormProtection::validateToken()
     * @return bool
     */
    public function verifyTokenForPoll(string $token, Poll $poll): bool
    {
        return $this->isFormProtectionNeeded()
            ? $this->getFormProtectionInstance()->validateToken($token, ...$this->createTokenGenerationParams($poll))
            : true;
    }

    /**
     * @return void
     * @see AbstractFormProtection::persistSessionToken()
     */
    public function persistSessionToken(): void
    {
        if ($this->isFormProtectionNeeded()) {
            $this->getFormProtectionInstance()->persistSessionToken();
        }
    }

    /**
     * @return void
     * @see AbstractFormProtection::clean()
     */
    public function clean(): void
    {
        if ($this->isFormProtectionNeeded()) {
            $this->getFormProtectionInstance()->clean();
        }
    }

    /**
     * @param Poll $poll
     * @return array
     */
    protected function createTokenGenerationParams(Poll $poll): array
    {
        return [
            'pollForm',
            'vote',
            $poll->getUid(),
        ];
    }

    /**
     * @return bool
     */
    protected function isFormProtectionNeeded(): bool
    {
        $formProtection = $this->getFormProtectionInstance();
        return !($formProtection instanceof \TYPO3\CMS\Core\FormProtection\DisabledFormProtection);
    }

    /**
     * @return AbstractFormProtection
     */
    protected function getFormProtectionInstance(): AbstractFormProtection
    {
        return FormProtectionFactory::get();
    }
}
