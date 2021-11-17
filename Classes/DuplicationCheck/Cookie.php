<?php
declare(strict_types=1);
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
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Cookie as SymfonyHttpFoundationCookie;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cookie duplication check
 *
 * Sets a cookie for every voted poll.
 *
 * @todo: make cookie settings configurable?
 */
class Cookie implements DuplicationCheckInterface
{
    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::isAvailable()
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::isVoted()
     */
    public function isVoted(Poll $poll): bool
    {
        // If the cookie is set, the poll is voted (cookie value does not matter)
        return array_key_exists($this->getCookieName($poll), $this->getRequest()->getCookieParams());
    }

    /**
     * {@inheritDoc}
     * @see DuplicationCheckInterface::registerVote()
     */
    public function registerVote(Poll $poll, Participation $participation): void
    {
        // Let the expiration date be calculated by PHP's date/time implementation. the timezone will be handeled internally (TYPO3 calls date_default_timezone_set() during bootstrap)
        $cookieExpire = \DateTime::createFromFormat('Y-m-d\TH:i:s', date('Y-m-d\TH:i:s', $GLOBALS['EXEC_TIME']))->add(new \DateInterval('P1Y'));
        $cookieDomain = GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY');
        $cookieSecure = (bool)GeneralUtility::getIndpEnv('TYPO3_SSL');
        $cookieSameSite = SymfonyHttpFoundationCookie::SAMESITE_LAX;

        $cookie = new SymfonyHttpFoundationCookie(
            $this->getCookieName($poll),
            '1',
            $cookieExpire,
            null,
            $cookieDomain,
            $cookieSecure,
            true,
            false,
            $cookieSameSite
        );
        header('Set-Cookie: ' . $cookie->__toString(), false);
    }

    /**
     * @param Poll $poll
     * @return string
     */
    protected function getCookieName(Poll $poll): string
    {
        return 'tx_minipoll-' . $poll->getUid();
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
