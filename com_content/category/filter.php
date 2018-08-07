<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$dispatcher = JEventDispatcher::getInstance();
$dispatcher->trigger('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));

$results = $dispatcher->trigger('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $dispatcher->trigger('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $dispatcher->trigger('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

$introcount = count($this->intro_items);
$counter = 0;
$doc = Factory::getDocument();
$css = "";



/* Layout Options */
$containerid = $this->params->get('containerid', 'isotope');

$gridcontainer = $this->params->get('gridcontainer', 'container');
$layoutmode = $this->params->get('layoutmode', 'square');

$introcolor = $this->params->get('introcolor', 'transparent');
$griditemcolor = $this->params->get('griditemcolor', '');


$this->introimagemode = $this->params->get('introimagemode', 'bg');
$this->fullimagemode = $this->params->get('fullimagemode', 'bg');
$this->afterdisplaytitle = $this->params->get('afterdisplaytitle', '1');
$this->beforedisplaycontent = $this->params->get('beforedisplaycontent', '1');
$this->afterdisplaycontent = $this->params->get('afterdisplaycontent', '1');
$this->excludefields = $this->params->get('excludefields', false);
if ($this->excludefields) {
	$this->excludefields = explode(",", $this->excludefields);
}

/* Filter Options */
$showfilters = $this->params->get('showfilters', '1');
$showfilterheadlines = $this->params->get('showfilterheadlines', '1');
$showfieldfilter = $this->params->get('customfieldsfilter', '1');

$excludefields = $this->params->get('excludefields', false);

$buttonclass = $this->params->get('buttonclass', 'btn btn-primary');
$checkedclass = $this->params->get('checkedclass', 'btn btn-primary');
$checkedbuttoncolor = $this->params->get('checkedbuttoncolor', '#000');


/* Overlay Options */
$contentoverlay = $this->params->get('contentoverlay', '1');
$overlaycolor = $this->params->get('overlaycolor', '#af18b5');
list($r, $g, $b) = sscanf($overlaycolor, "#%02x%02x%02x");
$a = $this->params->get('overlayopacity', '0.75');
$textcolor = $this->params->get('textcolor', '#fff');
$linkcolor = $this->params->get('linkcolor', '#fff');

$zoomintroimage = $this->params->get('zoomintroimage', false);
$zoomtext = 1 / $zoomintroimage;

if ($contentoverlay == 1) :
	$css .= '.overlay { transition: .5s ease; opacity: 0; 
						text-align: center;	background:rgba(' . $r .',' . $g .',' . $b .',' .$a .');
						width:100%;	height:100%;}';
	$css .= '.grid-item-content:hover .overlay { opacity: 1; cursor: pointer;}';
	$css .= '.text { color: ' . $textcolor .'; padding:10%;}';
	$css .= '.overlay .text a { color: ' . $linkcolor .'}';
endif;

/* check the css framework and assign framework specific classes */

$this->cssframework = $this->params->get('cssframework', 'bs4');
$this->gridcols = $this->params->get('gridcols', '6');


if ($cssframework == 'bs4') {
	$this->mediumclass = 'col-lg-' . $this->gridcols  . ' col-md-' . $this->gridcols  . ' col-sm-6';
	$this->smallclass = 'col-12';
}

if ($cssframework == 'bs3') {
	$this->mediumclass = 'col-md-' . $this->gridcols  . ' col-sm-6';
	$this->smallclass = 'col-xs-12';
}

if ($cssframework == 'bs2') {
	$this->mediumclass = 'span' . $this->gridcols  . ' span6';
}

if ($cssframework == 'none') {
	$this->mediumclass = '';
	$this->smallclass = '';
}


$css .= ".is-checked {background:$checkedbuttoncolor;}";
$css .= ".element-item {background:$griditemcolor; margin-bottom:30px;}";
$css .= ".overlay .btn-outline-light:hover {color:$overlaycolor;}";

if ($layoutmode == 'square') :
	$css .= '.square {width:100%;}';
	$css .= '.square:after {content: "";  display: block; padding-bottom: 100%; } ';
	$css .= '.content {position: absolute; width: 100%; height: 100%;}';
endif;

if ($zoomintroimage) :
	$css .=
		<<<CSS


	
	.grid-item-content {
		overflow: hidden;
		position: relative;
		cursor: pointer;
	}

	.content {
		height: 100%;
		width: 100%;
		background-size: cover;
		background-repeat: no-repeat;
		-webkit-transition: all .5s;
		-moz-transition: all .5s;
		-o-transition: all .5s;
		transition: all .5s;
	}
	
	.text {
		-ms-transform: scale(0);
		-moz-transform: scale(0);
		-webkit-transform: scale(0);
		-o-transform: scale(0);
		transform: scale(0);
		-webkit-transition: all .5s;
		-moz-transition: all .5s;
		-o-transition: all .5s;
		transition: all .5s;
	}

	.grid-item-content:hover .content, .grid-item-content:focus .content {
		-ms-transform: scale($zoomintroimage);
		-moz-transform: scale($zoomintroimage);
		-webkit-transform: scale($zoomintroimage);
		-o-transform: scale($zoomintroimage);
		transform: scale($zoomintroimage);
	}
	
		.grid-item-content:hover .text, .grid-item-content:focus .text {
		-ms-transform: scale($zoomtext);
		-moz-transform: scale($zoomtext);
		-webkit-transform: scale($zoomtext);
		-o-transform: scale($zoomtext);
		transform: scale($zoomtext);
	}

	.grid-item-content:hover .content:before, .grid-item-content:focus .content:before {
		display: block;
	}


	.grid-item-content:hover a, .grid-item-content:focus a {
		display: block;
	}
	
	.standard {
		width:100%;	
		height:35%;
		background: $introcolor;
		color: #fff;
		position: absolute;
		bottom:0;
		padding:1rem;
	}
	
	.overlay {
	height: 100%;
width: 100%;
display: table;
	}
	
	.btn-outline-secondary {
	border-color:#fff;
	}
	
	
	.overlay .text a.btn-outline-secondary:hover {
	background:#fff;
	color: rgba(255,89,0,1);
	border:none;
	}
	
	.overlay > div {
	display: table-cell;
	vertical-align: middle;
	}
	
	.grid-item-content:hover .standard {
		opacity: 0;
		transition: .5s ease; opacity: 0; 
	}


CSS;


endif;





JHtml::_('jquery.framework');
JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.4/isotope.pkgd.min.js', array('version' => 'auto', 'relative' => true), array('defer' => true, 'async' => false));
JHtml::_('script', 'https://imagesloaded.desandro.com/imagesloaded.pkgd.js', array('version' => 'auto', 'relative' => true), array('defer' => true, 'async' => false));

$doc->addStyleDeclaration($css);

function specialchars($string)
{
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´", "/", "-", " ", ",", "_");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "", "", "", "", " ", "");
	return strtolower(str_replace($search, $replace, $string));
}

function replacechars($string)
{
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´", "/", "-", " ", ",", "_");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "-", "-", "-", "-", "-", "-");
	return strtolower(str_replace($search, $replace, $string));
}


?>

<?php if ($showfilters == '1') :

	include 'filter_filter.php';

endif; ?>

<div id="msg-box" class="alert alert-primary" style="display:none;">
	<div><?php echo JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH');?></div>
</div>

<div id="isotopeContainer"></div>

<div id="<?php echo $containerid; ?>" class="<?php echo $gridcontainer; ?>">
	<div class="row grid mt-5">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>

		<div class="grid-sizer <?php echo $this->mediumclass; ?>"></div>
		<div class="gutter-sizer"></div>

		<?php
		if (!empty($this->intro_items)) :
			foreach ($this->intro_items as $key => &$item) :
				$this->item = &$item;
				$params = $this->item->params;
				$canEdit = $this->item->params->get('access-edit');
				$info = $params->get('info_block_position', 0);

				// Check if associations are implemented. If they are, define the parameter.
				$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));
				echo $this->loadTemplate('items');
				echo $this->item->event->afterDisplayContent;
				$counter++;
			endforeach;
		endif; ?>

	</div>
</div>

<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
		<?php endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>

<script>
	jQuery(document).ready(function () {

// init Isotope
		var $grid = jQuery('.grid').isotope({
			itemSelector: '.element-item',
			percentPosition: true,
			masonry: {
				columnWidth: '.grid-sizer',
				gutter: '.gutter-sizer'
			}
		});

// store filter for each group
		var filters = {};

		jQuery('.filters').on('click', 'button', function () {
			var $this = jQuery(this);
			// get group key
			var $buttonGroup = $this.parents('.button-group');
			var filterGroup = $buttonGroup.attr('data-filter-group');
			// set filter for group
			filters[filterGroup] = $this.attr('data-filter');
			// combine filters
			var filterValue = concatValues(filters);
			// set filter for Isotope
			$grid.isotope({filter: filterValue});

			// display message box if no filtered items
			if ( !$grid.data('isotope').filteredItems.length ) {
				jQuery('#msg-box').show();
			}

		});

// change is-checked class on buttons
		jQuery('.button-group').each(function (i, buttonGroup) {
			var $buttonGroup = jQuery(buttonGroup);
			$buttonGroup.on('click', 'button', function () {
				$buttonGroup.find('.<?php echo $checkedclass; ?>').removeClass('<?php echo $checkedclass; ?>');
				jQuery(this).addClass('<?php echo $checkedclass; ?>');
			});
		});

// flatten object by concatting values
		function concatValues(obj) {
			var value = '';
			for (var prop in obj) {
				value += obj[prop];
			}
			return value;
		}

		$grid.imagesLoaded().progress(function () {
			$grid.isotope('layout');
		});


	});
</script>