.. include:: ../../Includes.txt

.. _section-configuration-typoscript:

TypoScript Reference
====================

.. _section-configuration-typoscript-constants:

Constants
---------

Some global values can be configured with constants in
:typoscript:`plugin.tx_minipoll.settings`.

.. _ts-const-plugin-tx-minipoll-captcha:

resultRenderer.defaultColorList
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. container:: table-row

   Property
       resultRenderer.defaultColorList

   Data type
       :ref:`list <t3tsref:data-type-list>`

   Description
          Define a list to show colorized answers. The list items are assigned
          to the answers in order of appearance. If the list is shorter than the
          answer count, it is simply repeated.

          Ex:

          .. code-block:: typoscript
        
              colors = red,green,blue
            
              // Colors assigned to answers:
              Answer1 => red
              Answer2 => green
              Answer3 => blue
              Answer4 => red
              ...
          
          This constant is used in setup as ``resultRenderer.global.colors``,
          see :ref:`ts-resultRenderer-global-colors`.

.. _section-configuration-typoscript-stup:

Setup
-----

The main configuration of the extension is done in
``plugin.tx_minipoll.settings``.


.. _section-configuration-typoscript-property-details:

Property details
^^^^^^^^^^^^^^^^

.. only:: html

    .. contents::
        :local:
        :depth: 1
        :backlinks: none

.. _ts-plugin-tx-minipoll-captcha:

captcha
"""""""

.. container:: table-row

   Property
       captcha

   Data type
       :ref:`boolean <t3tsref:data-type-bool>` or :ref:`string <t3tsref:data-type-string>`

   Description
       Configure the usage of a captcha:

       ``0`` = Disable captcha

       ``1`` = Enable cptcha and use the default captchaProvider (see
       :ref:`emconf-defaultCaptchaProvider`).

       ``<alias>`` = Enable captcha and use the captchaProvider with this alias.

       .. tip::

           Developers who might want to implement their own captcha should refer
           to :ref:`section-developer-captcha`.


.. _ts-plugin-tx-minipoll-excludeAlreadyDisplayedPolls:

excludeAlreadyDisplayedPolls
""""""""""""""""""""""""""""

.. container:: table-row

   Property
          excludeAlreadyDisplayedPolls

   Data type
          :ref:`boolean <t3tsref:data-type-bool>`

   Description
          If set, the already displayed polls won't show up again. This could happen
          when multiple minipoll plugins are placed on the same page.


.. _ts-plugin-tx-minipoll-resultRenderer:

resultRenderer
""""""""""""""

.. container:: table-row

   Property
       resultRenderer

   Data type
       array

   Description
       Configuration of the result renderers. You'll find the detailed
       explanation for the properties in :ref:`section-configuration-resultRenderer`.


.. _ts-plugin-tx-minipoll-preserveGETVars:

preserveGETVars
"""""""""""""""

.. container:: table-row

   Property
          preserveGETVars

   Data type
          :ref:`boolean <t3tsref:data-type-bool>`/:ref:`list <t3tsref:data-type-list>`

   Description
          With this option you can configure, which GET parameters from a
          request should be preserved when gernerating links inside the
          extension.

          This should come in handy, when the poll plugin is called in the
          context of another one. For example, if a news-system is extended with
          minipoll polls.

          Possible options:

          ``0`` = Do not preserve any GET parameters

          ``1`` = Preserve all GET parameters (use with caution!)

          ``<list>`` = Preserve all GET parameters from the current request,
          which appear in this comma-separated list.

          Example:

          .. code-block:: typoscript

              plugin.tx_minipoll.settings {
                  preserveGETVars = tx_ttnews[tt_news]
              }

          Default: 0

       .. important::

           Following reserved GET parameter names will be ignored: ``id``,
           ``type``, ``cHash``, ``MP`` and ``eID``.
