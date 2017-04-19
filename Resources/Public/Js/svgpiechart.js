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
$.fn.svgpiechart=function(t){if("destroy"===t)return this.destroy(),this;if(this.length){var s=$.extend({},$.fn.svgpiechart.defaults,t),i=this.find(s.selectors.svg),e=i.find(s.selectors.slices),o=this.find(s.selectors.tooltip);return e.on("mouseover mouseout",function(t){if("mouseout"==t.type)return void o.removeClass(s.tooltipVisibleClass);o.text($(t.target.parentNode).data("tooltipvalue")),o.addClass(s.tooltipVisibleClass)}),i.on("mousemove",function(t){o.css({position:"fixed",left:t.clientX+s.tooltipOffset,top:t.clientY+s.tooltipOffset})}),this.destroy=function(){o.removeClass(s.tooltipVisibleClass),o.text(""),o.css({position:"",left:"",top:""})},this}},$.fn.svgpiechart.defaults={selectors:{svg:"svg",slices:".slice",tooltip:".tx_minipoll-svgpiechart-tooltip"},tooltipVisibleClass:"tx_minipoll-svgpiechart-tooltip-visible",tooltipOffset:10};