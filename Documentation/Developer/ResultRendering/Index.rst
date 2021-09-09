.. include:: ../../Includes.txt

.. _section-developer-resultrendering:

================
Result rendering
================

Minpoll brings an easy way to add your own result rendering.

.. _section-developer-resultrendering-addownresultrenderer:

Add your own resultRenderer
===========================

Add a resultRenderer class, that implements
``AawTeam\Minipoll\ResultRenderer\ResultRendererInterface``. For further
explanation of the interface, see the code, the methods are documented there.

.. code-block:: php

    namespace My\Extension\ResultRenderer;
    
    class MyFancyRenderer implements \AawTeam\Minipoll\ResultRenderer\ResultRendererInterface
    {
        // ...
    }

Next, add a view partial in ``Poll/Results/MyFancyRenderer.html``. The name is
composed by the "base path" ``Poll/Results``, followed by the last part of the
class name (``My\Extension\ResultRenderer\MyFancyRenderer`` becomes
``MyFancyRenderer``).

The partial will be invoked inside ``Poll/Results.html``. Within the partial, a
variable ``{results}``, which is an array, is available. Its contents come from
your ResultRenderer's method ``getRenderedResults()``.

Proceeding further, you can register your ResultRenderer class with an alias
("myfancy" in this case) in ``ext_localconf.php``:

.. code-block:: php

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['minipoll']['resultRenderers']['myfancy'] =
        \My\Extension\ResultRenderer\MyFancyRenderer::class;

By now, you can include your ResultRenderer in TypoScript setup (see also:
:ref:`section-configuration-resultRenderer-chooserenderers`):

.. code-block:: typoscript

    plugin.tx_minipoll.settings.resultRenderer.show := addToList(myfancy)

.. _section-developer-resultrendering-abstractrenderer:

The AbstractResultRenderer class
================================

The abstract class ``AawTeam\Minipoll\ResultRenderer\AbstractResultRenderer`` is
currently used to provide common functionalities such as taking care of the
lists/optionSplits (see :ref:`ts-resultRenderer-global-colors`) or loading and
sorting the PollOption objects as
``AawTeam\Minipoll\ViewModel\PollOptionViewModel``. This class is not yet meant
to be public API, but is targeted to become it in future.

So, if you know what you are doing and if you can live with code changes in the
future, feel free to extend ``AbstractResultRenderer``. Any feedbacks via the
issue tracker would be apreciated.
