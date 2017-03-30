.. include:: ../Includes.txt


.. _section-admin-manual:

Administrator Manual
====================

.. _section-installation:

Installation
------------

To install the extension, perform the following steps:

#. Go to the Extension Manager
#. Install the extension
#. Add the static template "Minipoll" to your typoscript template

.. _section-admin-captcha:

Captcha Integration
-------------------

Minipoll is built to work with any captcha implementation. Out-of-the-box, the
TYPO3 extension sr_freecap
(https://typo3.org/extensions/repository/view/sr_freecap) is supported. Others
might follow.

.. tip::
      
    Developers who might want to implement their own captcha should refer to
    :ref:`section-developer-captcha`.

.. _section-admin-templating:

Templating
----------

Templating is realized with fluid. The templates are fairly easy, most of the
work is done in partials. By default no layout view scripts are being used.
Register you own templates/partials the intended way:

.. code-block:: typoscript

    plugin.tx_minipoll.view {
        templateRootPaths.10 = fileadmin/mytemplates/minipoll/Templates
        partialRootPaths.10 = fileadmin/mytemplates/minipoll/Partials
    }

The templates and partials are not documented here, you have to take a look into
the code.

.. important::

    The template/partial structure and usage is not meant to be changed in
    bugfix releases (say: from 1.0.0 until 1.0.x), except there is a concrete
    need. This constraint might be extended to minor releases (say: from 1.0.0
    until 1.x.x) in the future.

.. _section-admin-typoscript-oldschool:

Adding a minipoll plugin with typoscript
----------------------------------------

If you want to add the plugin via Typoscript the "old school" way, the extension
provides a static template right for this purpose: "Minipoll (Old school)". When
it is included, you can use ``plugin.tx_minipoll`` as cObject.

Without flexforms in place you need to define what to display:

.. code-block:: typoscript

   lib.polls = < plugin.tx_minipoll
   lib.polls {
       # Can be 'list' or 'detail'
       display = detail

       # When display is set to 'detail', you must define which poll to display
       settings.pollUid = 2

       # .. or even use stdWrap
       settings.pollUid.data = register:aSpecialPollUid
   }

