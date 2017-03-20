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

use AawTeam\Minipoll\Domain\Model\Answer;
use AawTeam\Minipoll\Domain\Model\Participation;
use AawTeam\Minipoll\Domain\Model\Poll;
use AawTeam\Minipoll\Domain\Model\PollOption;
use AawTeam\Minipoll\Domain\Repository\PollRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Poll controller
 */
class PollController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \AawTeam\Minipoll\Domain\Repository\PollRepository
     * @inject
     */
    protected $pollRepository;

    /**
     * @var \AawTeam\Minipoll\Domain\Repository\ParticipationRepository
     * @inject
     */
    protected $participationRepository;

    /**
     * @var \AawTeam\Minipoll\Utility\PollUtility
     * @inject
     */
    protected $pollUtility;

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
                $this->forward('detail', null, null, ['poll' => $pollUid]);
                break;
            default :
                return 'Error: you must specify either "list" or "detail" in the display property of this cObject!';
        }

        return 'Error: something went terribly wrong!';
    }

    public function listAction()
    {
        $this->view->assign('polls', $this->pollRepository->findAll());
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
                return 'Error: you must define a poll uid';
            }
            $poll = $this->pollRepository->findByIdentifier($pollUid);
        }
        $this->view->assign('poll', $poll);
    }

    /**
     *
     * @param \AawTeam\Minipoll\Domain\Model\Poll $poll
     * @param array $answers
     * @param array $hp
     * @return string
     */
    public function voteAction(\AawTeam\Minipoll\Domain\Model\Poll $poll, array $answers = null, array $hp = null)
    {
        // Honeypot check
        if ($hp['one'] !== '' || $hp['two'] !== '') {
            return 'Error: invalid arguments';
        }

        if ($poll->getIsClosed()) {
            return 'Voting is closed';
        }

        if (!$this->pollUtility->canVoteInPoll($poll)) {
            return 'You cannot vote in this poll';
        }

        // Base-check $answers
        if (empty($answers) || (!$poll->getAllowMultiple() && count($answers) > 1)) {
            return 'Error: invalid arguments';
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
            return 'Error: invalid arguments';
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

        // @TODO: Add a user message!

        // Redirect to stats action
        $this->redirect('showResult', null, null, ['poll' => $poll->getUid()]);

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
        if (!$this->pollUtility->canDisplayResultsInPoll($poll)) {
            // @TODO: Add user message

            // Forward to detailAction
            $this->forward('detail', null, null, ['poll' => $poll->getUid()]);

            // This should never happen..
            return 'Error: something went terribly wrong!';
        }
        $this->view->assign('poll', $poll);
    }

    /**
     * @return array
     */
    protected function getTyposcriptConfiguration()
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settings = $frameworkConfiguration['settings'];
        $typoscriptConfiguration = \array_diff_key($frameworkConfiguration, \array_flip(['mvc', 'persistence', 'features', 'userFunc', 'extensionName', 'pluginName', 'vendorName', 'view', 'controllerConfiguration', 'settings']));

        /** @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoscriptService */
        $typoscriptService = $this->objectManager->get(\TYPO3\CMS\Extbase\Service\TypoScriptService::class);
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
