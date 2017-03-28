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
$.fn.svgpiechart=function(t){if("destroy"===t)return this.destroy(),this;if(this.length){var s=$.extend({},$.fn.svgpiechart.defaults,t),e=this.find(s.selectors.svg),o=e.find(s.selectors.slices),i=this.find(s.selectors.tooltip);return o.on("mouseover mouseout",function(t){if("mouseout"==t.type)return void i.removeClass(s.tooltipVisibleClass);i.text($(t.target.parentNode).data("tooltipvalue")),i.addClass(s.tooltipVisibleClass)}),e.on("mousemove",function(t){i.css({left:t.offsetX+s.tooltipOffset,top:t.offsetY+s.tooltipOffset})}),this.destroy=function(){i.removeClass(s.tooltipVisibleClass),i.text(""),i.css({left:"",top:""})},this}},$.fn.svgpiechart.defaults={selectors:{svg:"svg",slices:".slice",tooltip:".tx_minipoll-svgpiechart-tooltip"},tooltipVisibleClass:"tx_minipoll-svgpiechart-tooltip-visible",tooltipOffset:10};