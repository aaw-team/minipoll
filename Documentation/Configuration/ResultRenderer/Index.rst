.. include:: ../../Includes.txt

.. _section-configuration-resultRenderer:

============================
ResultRenderer Configuration
============================

The poll results are displayed by an extensible system named resultRenderers.

.. tip::
      
    Developers who might want to implement their own resultRenderer should refer
    to :ref:`section-developer-resultrendering`.

Minipoll comes with two built-in resultRenderers, whose options will be
explained below:
:ref:`section-configuration-resultRenderer-css` and
:ref:`section-configuration-resultRenderer-svgpiechart`.

.. _section-configuration-resultRenderer-chooserenderers:

Choose which resultRenderers to use
===================================

To use one or more resultRenderers, add them to the list in
``resultRenderer.show``. Note that the order of the list represents the order
in which the resultRenderers are invoked.

.. code-block:: typoscript

    plugin.tx_minipoll.settings.resultRenderer {
        show = css,svgpiechart
    }


.. _section-configuration-resultRenderer-global:

Global Options
==============

This section describes the options that are available to all the
resultRenderers. The global options are merged with those of the actual result
renderer configuration.

In the following **example**, the *css* resultRenderer displays the options in
default order, while *svgpiechart* uses the global ordering (``answers``) but
reverses the direction:

.. code-block:: typoscript

    plugin.tx_minipoll.settings.resultRenderer {
        show = css,svgpiechart
        global {
            orderBy = answers
            reverseOrder = 0
        }
        css {
            orderBy =
        }
        svgpiechart {
            reverseOrder = 1
        }
    }


.. important::

    Clearing a value from global inside the actual resultRenderer configuration
    does not work with ``>``.

    .. code-block:: typoscript

        global {
            orderBy = answers
        }
        css {
            # Does not work:
            orderBy >

            # Do it this way:
            orderBy =
        }

Available Global Options
------------------------

.. only:: html

    .. contents::
        :local:
        :depth: 1
        :backlinks: none

.. _ts-resultRenderer-global-orderBy:

orderBy
"""""""

.. container:: table-row

   Property
          orderBy

   Data type
          string

   Description
          Define, how the results are ordered before displaying. Currently available
          options:
    
          ``answers`` = order by answer count
    
          Default is the order defined in the poll record.

.. _ts-resultRenderer-global-reverseOrder:

reverseOrder
""""""""""""

.. container:: table-row

   Property
          reverseOrder

   Data type
          bool

   Description
          Reverse the direction of the order defined in
          :ref:`ts-resultRenderer-global-orderBy` 

          Default: 0

.. _ts-resultRenderer-global-colors:

colors
""""""

.. container:: table-row

   Property
          colors

   Data type
          :ref:`list <data-type-list>`/:ref:`optionSplit <objects-optionsplit>`

   Description
          Define a list to add colors to the displayed bars. The list items are
          assigned to the answers in order of appearance. If the list is shorter
          than the answer count, it is simply repeated.

          Ex:

          .. code-block:: typoscript
        
              colors = red,green,blue
            
              // Colors assigned to answers:
              Answer1 => red
              Answer2 => green
              Answer3 => blue
              Answer4 => red

          **useOptionSplit**

          If set to 1, the list is not interpreted as comma separated list, but
          as optionSplit
    
          Ex:

          .. code-block:: typoscript

              colors = red |*| green |*| blue
              colors.useOptionSplit = 1

              // Colors assigned to answers:
              Answer1 => red
              Answer2 => green
              Answer3 => green
              Answer4 => blue
          
          Default is the value in
          ``{$plugin.tx_minipoll.settings.resultRenderer.defaultColorList}``
          (see also :ref:`ts-const-plugin-tx-minipoll-captcha`),

.. _ts-resultRenderer-global-cssClasses:

cssClasses
""""""""""

.. container:: table-row

   Property
          cssClasses

   Data type
          :ref:`list <data-type-list>`/:ref:`optionSplit <objects-optionsplit>`

   Description
          This works exactly the same way as describend in
          :ref:`ts-resultRenderer-global-colors`.


.. _section-configuration-resultRenderer-css:

ResultRenderer Css
==================

This renderer displays the results as bars, using, well, CSS. It uses the global
options only and defines no additional ones.

.. _section-configuration-resultRenderer-svgpiechart:

ResultRenderer Svgpiechart
==========================

This renderer creates a piechart of the results as an inline svg by creating the
svg-xml on-the-fly. It uses the global options and defines following additional
ones:

.. only:: html

    .. contents::
        :local:
        :depth: 1
        :backlinks: none

.. _ts-resultRenderer-svgpiechart-includeJquery:

includeJquery
^^^^^^^^^^^^^

.. container:: table-row

   Property
          includeJquery

   Data type
          bool

   Description
          If enabled, a self hosted version of jQuery will be included in every
          page containing a poll result (that is rendered as svgpiechart). This
          is needed (and done) only when
          :ref:`ts-resultRenderer-svgpiechart-includeTooltipJs` is true too.

.. _ts-resultRenderer-svgpiechart-includeTooltipJs:

includeTooltipJs
^^^^^^^^^^^^^^^^

.. container:: table-row

   Property
          includeTooltipJs

   Data type
          bool

   Description
          If enabled, a jQuery based javascript is loaded to display a nice
          tooltip when hovering over the pie slices.

.. _ts-resultRenderer-svgpiechart-width:

width
^^^^^

.. container:: table-row

   Property
          width

   Data type
          int+

   Description
          Defines the width of the svg in pixels.

.. _ts-resultRenderer-svgpiechart-height:

height
^^^^^^

.. container:: table-row

   Property
          height

   Data type
          int+

   Description
          Defines the height of the svg in pixels.

.. _ts-resultRenderer-svgpiechart-radius:

radius
^^^^^^

.. container:: table-row

   Property
          radius

   Data type
          int+

   Description
          Defines the radius of the circle in the svg in pixels.

.. _ts-resultRenderer-svgpiechart-textRadius:

textRadius
^^^^^^^^^^

.. container:: table-row

   Property
          textRadius

   Data type
          int+

   Description
          Defines the position of the text inside a slice in the svg. The value
          is in pixels counted from the center of the circle.
    