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
            'left': event.offsetX + opts.tooltipOffset,
            'top': event.offsetY + opts.tooltipOffset
        });
    });

    // destroy
    this.destroy = function() {
        // remove classes
        $tooltip.removeClass(opts.tooltipVisibleClass);
        $tooltip.text('');
        $tooltip.css({
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

$('.tx_minipoll-svgpiechart-container').svgpiechart();
