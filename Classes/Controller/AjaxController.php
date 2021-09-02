<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\Controller;

/*
 * Copyright 2021 Agentur am Wasser | Maeder & Partner AG
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
use AawTeam\Minipoll\Domain\Model\Poll;
use AawTeam\Minipoll\Domain\Model\PollOption;
use AawTeam\Minipoll\Domain\Repository\ParticipationRepository;
use AawTeam\Minipoll\Utility\FormProtectionUtility;
use AawTeam\Minipoll\Utility\LocalizationUtility;
use AawTeam\Minipoll\Utility\PollUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Poll AjaxController
 */
class AjaxController extends ActionController
{
    /**
     * @var PollUtility
     */
    protected $pollUtility;

    /**
     * @var FormProtectionUtility
     */
    protected $formProtectionUtility;

    /**
     * @var ParticipationRepository
     */
    protected $participationRepository;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

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
     * @param ParticipationRepository $participationRepository
     */
    public function injectParticipationRepository(ParticipationRepository $participationRepository)
    {
        $this->participationRepository = $participationRepository;
    }

    /**
     * @param PersistenceManagerInterface $persistenceManager
     */
    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * @param Poll $poll
     * @return string
     */
    protected function pollDetailAction(Poll $poll)
    {
        $response = [];

        $response['messages'] = [
            htmlspecialchars('Rendering the poll: ' . $poll->getTitle()),
        ];

        $this->view->assignMultiple([
            'poll' => $poll,
        ]);
        $pollArray = []; //$this->pollUtility->pollToArray($poll);
        $pollArray['html'] = $this->view->render();
        $response['poll'] = $pollArray;

        return json_encode($response);
    }

    protected function showPollResultAction(Poll $poll)
    {
        $response = [];
        if (!$this->pollUtility->canDisplayResultsInPoll($poll)) {
            // Add user message
            $response['messages'] = [
                LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.cannotDisplayResults'),
            ];
        } else {
            $pollArray = []; //$this->pollUtility->pollToArray($poll);
            $this->view->assignMultiple([
                'poll' => $poll,
            ]);
            $pollArray['html'] = $this->view->render();
            $response['poll'] = $pollArray;
        }

        return json_encode($response);
    }

    protected function votePollAction(Poll $poll, array $answers = null, array $hp = null, ?string $captcha = null, ?string $csrfToken = null)
    {
//         DebuggerUtility::var_dump([
//             '$answers' => $answers,
//             '$hp' => $hp,
//             '$captcha' => $captcha,
//             '$csrfToken' => $csrfToken,
//             'request' => $this->request,
//         ]);
//         die();

        $messages= [];



//         if (Registry::isVotedPoll($poll->getUid())) {
//             // Forward with no message, this will not display anything
//             $this->forward('displayMessage');
//         }
//         Registry::addVotedPoll($poll->getUid());

        // CSRF check
        if (!$this->formProtectionUtility->verifyTokenForPoll($csrfToken, $poll)) {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.csrfToken');
            return json_encode(['messages' => $messages]);
        }
        //$this->formProtectionUtility->clean();

        // Honeypot check
        if ($hp['one'] !== '' || $hp['two'] !== '') {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.honeypotCheck');
            return json_encode(['messages' => $messages]);
        }

        if ($poll->getIsClosed()) {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.votingIsClosed');
            return json_encode(['messages' => $messages]);
        }

        if (!$this->pollUtility->canVoteInPoll($poll)) {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.cannotVoteInPoll');
            return json_encode(['messages' => $messages]);
        }

        // Check captcha
        $captchaProviderAlias = $this->pollUtility->getCaptchaProviderAliasFromSettings($this->settings);
        if ($captchaProviderAlias !== false && $poll->getUseCaptcha()) {
            // Test the argument
            if (!\is_string($captcha) || empty($captcha)) {
                $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.emptyCaptcha');
                return json_encode(['messages' => $messages]);
            }

            try {
                $captchaProvider = CaptchaProviderFactory::getCaptchaProvider($captchaProviderAlias);
                if (!$captchaProvider->validate($captcha, $poll)) {
                    $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.invalidCaptcha');
                    return json_encode(['messages' => $messages]);
                }
            } catch (\AawTeam\Minipoll\Exception\NoCaptchaProviderFoundException $e) {
                // Silently fail here as the captcha field would not be rendered either
            }
        }

        // Base-check $answers
        if (empty($answers)) {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.emptyAnswer');
            return json_encode(['messages' => $messages]);
        } elseif (!$poll->getAllowMultiple() && count($answers) > 1) {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.invalidAnswer');
            return json_encode(['messages' => $messages]);
        }

        // Filter $answers
        $answers = array_filter(array_map('intval', $answers));

        // Check whether answers exist (as options) in this poll
        $options = [];
        /** @var PollOption $pollOption */
        foreach ($poll->getOptions() as $pollOption) {
            $options[$pollOption->getUid()] = $pollOption;
        }
        $differences = array_diff($answers, array_keys($options));
        if (!empty($differences)) {
            $messages[] = LocalizationUtility::translate('message.error.title') . ' ' . LocalizationUtility::translate('message.error.invalidAnswer');
            return json_encode(['messages' => $messages]);
        }

        // Create Answer objects
        /** @var ObjectStorage $answerObjects */
        $answerObjects = GeneralUtility::makeInstance(ObjectStorage::class);
        foreach ($answers as $optionUid) {
            /** @var Answer $answer */
            $answer = GeneralUtility::makeInstance(Answer::class);
            $answer->setPid($poll->getPid());
            $answer->setPollOption($options[$optionUid]);
            $answerObjects->attach($answer);
        }

        // Create a new participation
        /** @var Participation $participation */
        $participation = GeneralUtility::makeInstance(Participation::class);
        $participation->setPoll($poll);
        $participation->setPid($poll->getPid());
        $participation->setIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        $participation->setAnswers($answerObjects);

        // Add frontendUser (if one is logged in)
        $context = GeneralUtility::makeInstance(Context::class);
        if ($context->getPropertyFromAspect('frontend.user', 'isLoggedIn')) {
            $frontendUser = $this->frontendUserRepository->findByIdentifier($context->getPropertyFromAspect('frontend.user', 'id'));
            $participation->setFrontendUser($frontendUser);
        }

        // Inform the duplication checker
        $this->pollUtility->disableVoteInPoll($poll, $participation);

        // Create the data
        $this->participationRepository->add($participation);

        // Persist data
        $this->persistenceManager->persistAll();

        // Add a user message!
        $messages[] = LocalizationUtility::translate('message.success.createParticipation');

        $response = [
            'messages' => $messages
        ];
        return json_encode($response);
    }
}
