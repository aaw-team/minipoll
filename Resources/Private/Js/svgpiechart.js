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
$.fn.svgpiechart = function(options) {
    if(options === 'destroy') {
        this.destroy();
        return this;
    }

    if(!this.length) {
        return;
    }

    // merge options
    var opts = $.extend({}, $.fn.svgpiechart.defaults, options);

    // get the elements
    var $svg = this.find(opts.selectors.svg);
    var $slices = $svg.find(opts.selectors.slices);
    var $tooltip = this.find(opts.selectors.tooltip);

    // register hover event
    $slices.on('mouseover mouseout', function(event) {
        if (event.type == 'mouseout') {
            $tooltip.removeClass(opts.tooltipVisibleClass);
            return;
        }

        $tooltip.text($(event.target.parentNode).data('tooltipvalue'));
        $tooltip.addClass(opts.tooltipVisibleClass);
    });

    // register mousemove event for tooltip
    $svg.on('mousemove', function(event) {
        $tooltip.css({
            'position': 'fixed',
            'left': event.clientX + opts.tooltipOffset,
            'top': event.clientY + opts.tooltipOffset
        });
    });

    // destroy
    this.destroy = function() {
        // remove classes
        $tooltip.removeClass(opts.tooltipVisibleClass);
        $tooltip.text('');
        $tooltip.css({
            'position': '',
            'left': '',
            'top': ''
        });
    };

    return this;
};

// default options
$.fn.svgpiechart.defaults = {
    selectors: {
        svg: 'svg',
        slices: '.slice',
        tooltip: '.tx_minipoll-svgpiechart-tooltip'
    },
    tooltipVisibleClass: 'tx_minipoll-svgpiechart-tooltip-visible',
    tooltipOffset: 10
};
