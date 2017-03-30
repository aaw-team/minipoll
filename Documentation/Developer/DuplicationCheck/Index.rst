.. include:: ../../Includes.txt

.. _section-developer-duplicationcheck:

Duplication Checking
====================

To disallow a participant to vote more than once in a poll, duplicationChecking
is in place. It is possible to register a user defined check when the default
checks do not fit your needs.

.. _section-developer-duplicationcheck-addownduplicationcheck:

Add your own duplicationCheck
-----------------------------

Add a duplicationCheck class, that implements
``AawTeam\Minipoll\DuplicationCheck\DuplicationCheckInterface``. For further
explanation of the interface, see the code, the methods are documented there.

.. code-block:: php

    namespace My\Extension\DuplicationCheck;
    
    class MyFancyChecker implements \AawTeam\Minipoll\DuplicationCheck\DuplicationCheckInterface
    {
        // ...
    }

Add your duplicationCheck to the select field of the poll record in
``Configuration/TCA/Overrides/tx_minipoll_poll.php``:

.. code-block:: php

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'tx_minipoll_poll',
        'duplication_check',
        [
            'My fancy check',
            \My\Extension\DuplicationCheck\MyFancyChecker::class
        ]
    );

Now your duplicationCheck can be selected in the poll record and will then be
invoked by the extension.

