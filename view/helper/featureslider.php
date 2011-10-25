<?php
	/**
	 *	Feature Slider Helper
	 *
	 *	@see IMPORTANT: To use this helper you MUST include jQuery into your page, to
	 *	do that, echo $html->script("script/jquery-1.6.4.min.js") inside the <head>
	 *	tag.
	 *
	 *	This help will convert a series of divs whose id is the "feat1", "feat2",
	 *	"feat3", etc.. In a jQuery powered slider which will show one "featN" at
	 *	a time and also buttons to change between the "featN" showd.
	 *	Usage (in view's code):
	 *	<code>
	 *		<div id="feat1">
	 *			Feature 1, may contain images or any other Divs
	 *		</div>
	 *		<div id="feat2">
	 *			You may create as many features as you want
	 *		</div>
	 *		<div id="feat3">
	 *			Feature 3 or include any code inside
	 *		</div>
	 *		<div id="feat4">
	 *			Last feature to show
	 *		</div>
	 *		<div class="selector">
	 *			<?php echo $featureslider->Selector(); //will echo the buttons and also the script that controll the feats ?>
	 *		</div>
	 *	</code>
	 *
	 *	@copyright Copyright 2011 Luiz Fernando Alves da Silva
	 *	@license zlib/png license
	 *
	 */
	class FeaturesliderHelper extends Helper
	{

		/**
		 *	Will echo the entire script and presentation time control and also
		 *	returns the selector buttons for the operation of such. The imageg
		 *	for the image parameteres must be in the resource/img dir.
		 *
		 *	Usage:
		 *	<code>
		 *		echo $featureslider->Selector(3500, "square.jpg", "tickedSquare.jpg");
		 *	</code>
		 *	
		 *	@param int Milliseconds for a feature change to the next one.
		 *	@param string Image for the button that will represent the existing features (in the return).
		 *	@param string Image for the button that will represent the current selected feature (in the return).
		 *
		 *	@return string An html+javascript code that create a dynamic selector menu of existing features.
		 */
		public function Selector( $slidetime = 3500, $selectorImg = "featselector.png", $selectedImg = "featselected.png")
		{
			$this->InitSlider( $slidetime );

			return "<img style='cursor: pointer;' onclick='showFeat(1);' id='featselector1' src='".ROOT.IMAGE_PATH.$selectorImg."' alt='setfeat1' /><img id='featselected1' src='".ROOT.IMAGE_PATH.$selectedImg."' alt='setfeat1' />";
		}

		private function InitSlider($slidetime = 3500)
		{
			?>
				<script>
					currentFeature=1;
					featTimer=setTimeout("showNextFeat()", <?php echo $slidetime; ?>);
			
					function showNewFeat()
					{
						$('#feat'+currentFeature).fadeIn(500);
					}
			
					function showFeat(i)
					{
						old = currentFeature;
						currentFeature = i;
						$('#feat'+old).fadeOut(250, showNewFeat);
						$('#featselected'+old).hide();
						$('#featselector'+old).show();
						$('#featselected'+i).show();
						$('#featselector'+i).hide();
			
						clearTimeout(featTimer);
						featTimer=setTimeout("showNextFeat()", <?php echo $slidetime; ?>);
					}
			
					function showNextFeat()
					{
						if($('#feat'+(currentFeature+1)).length < 1)
						{
							showFeat(1);
						}else{
							showFeat(currentFeature+1);
						}
						
					}
			
					$(document).ready(function()
					{
						var i=2;
						while ( $('#feat'+i).length )
						{
							$('#feat'+i).hide();
			
							$('#featselected'+(i-1)).after("<img style='cursor: pointer;' onclick='showFeat("+i+");' id='featselector"+i+"' src='"+$('#featselector'+(i-1)).			attr('src')+"' alt='setfeat"+i+"' /><img id='featselected"+i+"' src='"+$('#featselected'+(i-1)).attr('src')+"' 			alt='setfeat"+i+"' />");
			
							$('#featselected'+i).hide();
			
							i++;
						}
						$('#featselector1').hide();
					});
				</script>
			<?php
		}
	}
?>