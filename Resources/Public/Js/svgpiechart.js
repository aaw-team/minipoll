$.fn.svgpiechart=function(t){if("destroy"===t)return this.destroy(),this;if(this.length){var s=$.extend({},$.fn.svgpiechart.defaults,t),e=this.find(s.selectors.svg),i=e.find(s.selectors.slices),o=this.find(s.selectors.tooltip);return i.on("mouseover mouseout",function(t){if("mouseout"==t.type)return void o.removeClass(s.tooltipVisibleClass);o.text($(t.target.parentNode).data("tooltipvalue")),o.addClass(s.tooltipVisibleClass)}),e.on("mousemove",function(t){o.css({left:t.offsetX+s.tooltipOffset,top:t.offsetY+s.tooltipOffset})}),this.destroy=function(){o.removeClass(s.tooltipVisibleClass),o.text(""),o.css({left:"",top:""})},this}},$.fn.svgpiechart.defaults={selectors:{svg:"svg",slices:".slice",tooltip:".tx_minipoll-svgpiechart-tooltip"},tooltipVisibleClass:"tx_minipoll-svgpiechart-tooltip-visible",tooltipOffset:10},$(".tx_minipoll-svgpiechart-container").svgpiechart();