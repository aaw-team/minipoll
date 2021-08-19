<?php
namespace AawTeam\Minipoll\Controller;

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

use AawTeam\Minipoll\CaptchaProvider\Factory as CaptchaProviderFactory;
use AawTeam\Minipoll\Domain\Model\Answer;
use AawTeam\Minipoll\Domain\Model\Participation;
use AawTeam\Minipoll\Domain\Model\PollOption;
use AawTeam\Minipoll\Domain\Repository\ParticipationRepository;
use AawTeam\Minipoll\Domain\Repository\PollRepository;
use AawTeam\Minipoll\Registry;
use AawTeam\Minipoll\Utility\FormProtectionUtility;
use AawTeam\Minipoll\Utility\LocalizationUtility;
use AawTeam\Minipoll\Utility\PollUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Poll controller
 */
class PollController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var PollRepository
     */
    protected $pollRepository;

    /**
     * @var ParticipationRepository
     */
    protected $participationRepository;

    /**
     * @var PollUtility
     */
    protected $pollUtility;

    /**
     * @var FormProtectionUtility
     */
    protected $formProtectionUtility;

    /**
     * @param PollRepository $pollRepository
     */
    public function injectPollRepository(PollRepository $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    /**
     * @param ParticipationRepository $participationRepository
     */
    public function injectParticipationRepository(ParticipationRepository $participationRepository)
    {
        $this->participationRepository = $participationRepository;
    }

    /**
     * @param PollUtility $pollUtility
     */
    public function injectPollUtility(PollUtility $pollUtility)
    {
        $this->pollUtility = $pollUtility;
    }

    /**
     * @param FormProtectionUtility $formProtectionUtility
     */
    public function injectFormProtectionUtility(FormProtectionUtility $formProtectionUtility)
    {
        $this->formProtectionUtility = $formProtectionUtility;
    }

    /**
     * Here we get when the plugin is included via typoscript (never via flexforms)
     * @return string
     */
    public function indexAction()
    {
        $configuration = $this->getTyposcriptConfiguration();

        $display = $configuration['display'];
        if ($configuration['display.']) {
            $display = $this->configurationManager->getContentObject()->stdWrap($display, $configuration['display.']);
        }

        switch ($display) {
            case 'list' :
                $this->forward('list');
                break;
            case 'detail' :
                $pollUid = $configuration['settings']['pollUid'];
                if ($configuration['settings']['pollUid.']) {
                    $pollUid = $this->configurationManager->getContentObject()->stdWrap($pollUid, $configuration['settings']['pollUid.']);
                }

                if ($pollUid < 1) {
                    $this->forwardToDisplayMessageAction('message.error.noPollUidFound', 'message.error.title', AbstractMessage::ERROR);
                    exit;
                }

                $this->forward('detail', null, null, ['poll' => $pollUid]);
                break;
            default :
                $this->forwardToDisplayMessageAction('message.error.unknownDisplayOption', 'message.error.title', AbstractMessage::ERROR);
                exit;
        }

        return 'Error: something went terribly wrong!';
    }

    /**
     * @return void
     */
    public function listAction()
    {
        $polls = $this->pollRepository->findAll();
        if ($this->settings['excludeAlreadyDisplayedPolls']) {
            foreach ($polls as $key => $poll) {
                if (Registry::isDisplayedPoll($poll->getUid())) {
                    $polls->offsetUnset($key);
                    continue;
                }
                Registry::addDisplayedPoll($poll->getUid());
            }
        }
        $this->view->assign('polls', $polls);
    }

    /**
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     * @return string
     */
    public function detailAction(\AawTeam\Minipoll\Domain\Model\Poll $poll = null)
    {
        if ($poll === null) {
            $pollUid = (int) $this->settings['pollUid'];

            if ($pollUid < 1) {
                $this->forwardToDisplayMessageAction('message.error.noPollUidFound', 'message.error.title', AbstractMessage::ERROR);
                exit;
            }
            $poll = $this->pollRepository->findByIdentifier($pollUid);
        }
        if ($this->settings['excludeAlreadyDisplayedPolls']) {
            if (Registry::isDisplayedPoll($poll->getUid())) {
                // Forward with no message, this will not display anything
                $this->forward('displayMessage');
            }
            Registry::addDisplayedPoll($poll->getUid());
        }
        $this->pollUtility->addPollToPageCache($poll);
        $this->view->assign('poll', $poll);
    }

    /**
     *
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     * @param array $answers
     * @param array $hp
     * @param string $captcha
     * @param string $csrfToken
     * @return string
     */
    public function voteAction(\AawTeam\Minipoll\Domain\Model\Poll $poll, array $answers = null, array $hp = null, $captcha = null, $csrfToken = null)
    {
        if (Registry::isVotedPoll($poll->getUid())) {
            // Forward with no message, this will not display anything
            $this->forward('displayMessage');
        }
        Registry::addVotedPoll($poll->getUid());

        // CSRF check
        $csrfCheckPassed = $this->formProtectionUtility->verifyTokenForPoll($csrfToken, $poll);
        $this->formProtectionUtility->clean();
        if ($csrfCheckPassed !== true) {
            $this->addErrorMessageAsFlashMessage('message.error.csrfToken');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
        }

        // Honeypot check
        if ($hp['one'] !== '' || $hp['two'] !== '') {
            $this->addErrorMessageAsFlashMessage('message.error.honeypotCheck');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
            exit;
        }

        if ($poll->getIsClosed()) {
            $this->addErrorMessageAsFlashMessage('message.error.votingIsClosed');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
            exit;
        }

        if (!$this->pollUtility->canVoteInPoll($poll)) {
            $this->addErrorMessageAsFlashMessage('message.error.cannotVoteInPoll');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
            exit;
        }

        // Check captcha
        $captchaProviderAlias = $this->pollUtility->getCaptchaProviderAliasFromSettings($this->settings);
        if ($captchaProviderAlias !== false && $poll->getUseCaptcha()) {
            // Test the argument
            if (!\is_string($captcha) || empty($captcha)) {
                $this->addErrorMessageAsFlashMessage('message.error.emptyCaptcha');
                $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
                exit;
            }

            try {
                $captchaProvider = CaptchaProviderFactory::getCaptchaProvider($captchaProviderAlias);
                if (!$captchaProvider->validate($captcha, $poll)) {
                    $this->addErrorMessageAsFlashMessage('message.error.invalidCaptcha');
                    $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
                    exit;
                }
            } catch (\AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException $e) {
                // Silently fail here as the captcha field would not be rendered either
            }
        }

        // Base-check $answers
        if (empty($answers)) {
            $this->addErrorMessageAsFlashMessage('message.error.emptyAnswer');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
            exit;
        } elseif (!$poll->getAllowMultiple() && count($answers) > 1) {
            $this->addErrorMessageAsFlashMessage('message.error.invalidAnswer');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
            exit;
        }

        // Filter $answers
        $answers = \array_filter(\array_map('intval', $answers));

        // Check whether answers exist (as options) in this poll
        $options = [];
        /** @var PollOption $pollOption */
        foreach ($poll->getOptions() as $pollOption) {
            $options[$pollOption->getUid()] = $pollOption;
        }
        $differences = \array_diff($answers, array_keys($options));
        if (!empty($differences)) {
            $this->addErrorMessageAsFlashMessage('message.error.invalidAnswer');
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);
            exit;
        }

        // Create Answer objects
        /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage $answerObjects */
        $answerObjects = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class);
        foreach ($answers as $optionUid) {
            /** @var Answer $answer */
            $answer = $this->objectManager->get(Answer::class);
            $answer->setPid($poll->getPid());
            $answer->setPollOption($options[$optionUid]);
            $answerObjects->attach($answer);
        }

        // Create a new participation
        /** @var Participation $participation */
        $participation = $this->objectManager->get(Participation::class);
        $participation->setPoll($poll);
        $participation->setPid($poll->getPid());
        $participation->setIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        $participation->setAnswers($answerObjects);

        // Add frontendUser (if one is logged in)
        if (\is_array($this->getTyposcriptFrontendController()->fe_user->user) && $this->getTyposcriptFrontendController()->fe_user->user['uid']) {
            /** @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository */
            $frontendUserRepository = $this->objectManager->get(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class);
            $frontendUser = $frontendUserRepository->findByIdentifier($this->getTyposcriptFrontendController()->fe_user->user['uid']);
            $participation->setFrontendUser($frontendUser);
        }

        // Inform the duplication checker
        $this->pollUtility->disableVoteInPoll($poll, $participation);

        // Create the data
        $this->participationRepository->add($participation);

        // Persist data
        /** @var $persistenceManager \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager */
        $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $persistenceManager->persistAll();

        // Flush cache
        $this->pollUtility->clearPageCacheByPoll($poll);

        // Add a user message!
        $this->addFlashMessage('message.success.createParticipation', '', AbstractMessage::OK);

        // Redirect to stats action
        // cannot use $this->redirect() as the current query string must be respected
        $uriBuilder = $this->uriBuilder->reset();
        $uriBuilder->setCreateAbsoluteUri(true)
                   ->setAddQueryString(true)
                   ->setAddQueryStringMethod('GET');

        \TYPO3\CMS\Core\Utility\HttpUtility::redirect($uriBuilder->uriFor('showResult', ['poll' => $poll->getUid()]));

        // This should never happen..
        return 'Error: something went terribly wrong!';
    }

    /**
     *
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     * @return string
     */
    public function showResultAction(\AawTeam\Minipoll\Domain\Model\Poll $poll)
    {
        if ($this->settings['excludeAlreadyDisplayedPolls']) {
            if (Registry::isDisplayedPoll($poll->getUid())) {
                // Forward with no message, this will not display anything
                $this->forward('displayMessage');
            }
            Registry::addDisplayedPoll($poll->getUid());
        }

        if (!$this->pollUtility->canDisplayResultsInPoll($poll)) {
            // Add user message
            $this->addErrorMessageAsFlashMessage('message.error.cannotDisplayResults');

            // Forward to detailAction
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);

            // This should never happen..
            return 'Error: something went terribly wrong!';
        }
        $this->pollUtility->addPollToPageCache($poll);
        $this->view->assign('poll', $poll);
    }

    /**
     * @param string $message
     * @param string $title
     * @param int $severity
     */
    protected function forwardToDisplayMessageAction($message = null, $title = '', $severity = AbstractMessage::OK)
    {
        if (\is_string($message) && !empty($message)) {
            $this->addFlashMessage($message, $title, $severity);
        }
        $this->forward('displayMessage');
    }

    /**
     * "Dead-end" method that only shows the flash messages.
     *
     * @param string $message
     * @param string $title
     * @param int $severity
     */
    public function displayMessageAction()
    {}

    /**
     * Extend parent method: translate $messageBody and $messageTitle
     *
     * {@inheritDoc}
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController::addFlashMessage()
     */
    public function addFlashMessage($messageBody, $messageTitle = '', $severity = AbstractMessage::OK, $storeInSession = true)
    {
        $messageBody = LocalizationUtility::translate($messageBody);
        if (\is_string($messageTitle) && $messageTitle !== '') {
            $messageTitle = LocalizationUtility::translate($messageTitle);
        }
        return parent::addFlashMessage($messageBody, $messageTitle, $severity, $storeInSession);
    }

    /**
     * @param string $messageBody
     * @param string $messageTitle
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController::addFlashMessage()
     */
    protected function addErrorMessageAsFlashMessage($messageBody, $messageTitle = null)
    {
        if ($messageTitle === null) {
            $messageTitle = 'message.error.title';
        }
        return $this->addFlashMessage($messageBody, $messageTitle, AbstractMessage::ERROR);
    }

    /**
     * @return array
     */
    protected function getTyposcriptConfiguration()
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settings = is_array($frameworkConfiguration['settings']) ? $frameworkConfiguration['settings'] : [];
        $typoscriptConfiguration = \array_diff_key($frameworkConfiguration, \array_flip(['mvc', 'persistence', 'features', 'userFunc', 'extensionName', 'pluginName', 'vendorName', 'view', 'controllerConfiguration', 'settings']));

        /** @var TypoScriptService $typoscriptService */
        $typoscriptService = $this->objectManager->get(TypoScriptService::class);
        $typoscriptConfiguration = $typoscriptService->convertPlainArrayToTypoScriptArray($typoscriptConfiguration);
        $typoscriptConfiguration['settings'] = $typoscriptService->convertPlainArrayToTypoScriptArray($settings);
        return $typoscriptConfiguration;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
