<?php
namespace AawTeam\Minipoll\ViewHelpers\Form;

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
use AawTeam\Minipoll\Utility\FormProtectionUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * CsrfTokenViewHelper
 */
class CsrfTokenViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var FormProtectionUtility
     */
    protected $formProtectionUtility;

    /**
     * @param FormProtectionUtility $formProtectionUtility
     */
    public function injectFormProtectionUtility(FormProtectionUtility $formProtectionUtility)
    {
        $this->formProtectionUtility = $formProtectionUtility;
    }

    /**
     * @var string
     */
    protected $tagName = 'input';

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('poll', Poll::class, 'The poll to create the csrf token for', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $name = $this->prefixFieldName('csrfToken');
        $this->registerFieldNameForFormTokenGeneration($name);
        $this->setRespectSubmittedDataValue(false);

        $this->tag->addAttribute('type', 'hidden');
        $this->tag->addAttribute('name', $name);
        $this->tag->addAttribute('value', $this->formProtectionUtility->generateTokenForPoll($this->arguments['poll']));
        $this->formProtectionUtility->persistSessionToken();

        $this->addAdditionalIdentityPropertiesIfNeeded();

        return $this->tag->render();
    }
}
