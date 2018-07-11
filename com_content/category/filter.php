<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;

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

$layoutmode = $this->params->get('layoutmode', 'square');
$showfilters = $this->params->get('showfilters', '1');
$showfilterheadlines = $this->params->get('showfilterheadlines', '1');
$overlaycolor = $this->params->get('overlaycolor', '#af18b5');
list($r, $g, $b) = sscanf($overlaycolor, "#%02x%02x%02x");
$a = $this->params->get('overlayopacity', '0.75');
$textcolor = $this->params->get('textcolor', '#fff');
$linkcolor = $this->params->get('linkcolor', '#fff');
$griditemcolor = $this->params->get('griditemcolor', '#ccc');
$contentoverlay = $this->params->get('contentoverlay', '1');
$checkedbuttoncolor = $this->params->get('checkedbuttoncolor', '#000');
$gridcontainer = $this->params->get('gridcontainer', 'container');
$buttonclass = $this->params->get('buttonclass', 'btn btn-primary');
$checkedclass = $this->params->get('checkedclass', 'btn btn-primary');
$excludefields = $this->params->get('excludefields', false);
$cssframework = $this->params->get('cssframework', 'bs4');

if ($cssframework == 'bs4') {
	$mediumclass = 'col-md-' . $this->params->get('gridcols', '6');
	$smallclass = 'col-12';
}

if ($cssframework == 'bs3') {
	$mediumclass = 'col-md-' . $this->params->get('gridcols', '6');
	$smallclass = 'col-xs-12';
}

if ($cssframework == 'bs2') {
	$mediumclass = 'span' . $this->params->get('gridcols', '6');
}

if ($excludefields) {
	$excludefields = explode(",",$excludefields);
}

$css = "";

$css .= ".is-checked {background:$checkedbuttoncolor;}";
$css .= ".element-item {background:$griditemcolor;}";

if ($layoutmode == 'square') :
	$css .= '.square {width:100%;}';
	$css .= '.square:after {content: "";  display: block; padding-bottom: 100%; } ';
	$css .= '.content {position: absolute; width: 100%; height: 100%;}';
endif;


if ($contentoverlay == 1) :
	$css .= '.overlay { transition: .5s ease; opacity: 0; 
						text-align: center;	background:rgba(' . $r .',' . $g .',' . $b .',' .$a .');
						width:100%;	height:100%;}';
	$css .= '.grid-item-content:hover .overlay { opacity: 1; cursor: pointer;}';
	$css .= '.text { color: ' . $textcolor .'}';
	$css .= '.overlay a { color: ' . $linkcolor .'}';
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

<?php if ($showfilters == '1') : ?>

	<?php
	$uniquetag = array();
	$uniquepaths = array();
	$parentnames = array();
	$uniquefields = array();

	foreach ($this->intro_items as $item) {

		if ($this->params->get('tagsfilter', '1')) :

			$tags = $item->tags->itemTags;

			foreach ($tags as $tag) {
				$uniquetag[$tag->id] = $tag;
				$uniquepaths[$tag->id] = $tag->path;
			}

			/* get the uniquepaths and push their value into an array */

			foreach ($uniquepaths as $id => $uniquepath) {
				list($tagparents, $child) = explode("/", $uniquepath);
				if (!in_array($tagparents, $parentnames, true)) {
					array_push($parentnames, $tagparents);
				}
			}

		endif;


		if ($this->params->get('customfieldsfilter', '1')) :
			/* get the fields and push their value into an array */

			$fields = $item->jcfields;

			foreach ($fields as $field) {
				if (!in_array($field->id, $excludefields)) {
					$uniquefields[$field->name] = $field;
					$fieldvalues[$field->name][] = array();
				}
			}

			foreach ($uniquefields as $name => $uniquefield) {
				$myvalues = explode(", ", $uniquefield->value);

				foreach ($myvalues as $myvalue) {
					if (!in_array($myvalue, $fieldvalues[$uniquefield->name], true) && (!empty($myvalue))) {
						array_push($fieldvalues[$uniquefield->name], $myvalue);
					}
				}
				$fieldvalues[$uniquefield->name] = array_filter($fieldvalues[$uniquefield->name]);
			}
		endif;
	}

	?>

	<div class="filters">

		<?php

		/* show the tagparents as headlines */
		foreach ($parentnames

		         as $parentname) {
			echo '<div class="ui-group my-3">';
			if ($showfilterheadlines == '1') :
				echo '<h3 class="parent">' . ucwords($parentname) . '</h3>';
			endif;
			?>
			<div class="button-group filters-button-group" data-filter-group="<?php echo $parentname; ?>">
				<button class="<?php echo $buttonclass; ?> <?php echo $checkedclass; ?> m-1"
				        data-filter=""><?php echo JText::_('CCC_FILTER_SHOW_ALL'); ?></button>
				<?php
				foreach ($uniquetag as $id => $tag) {
					/* put the tags below their parents */
					list($parent, $child) = explode("/", $tag->path);
					if ($parentname == $parent) { ?>
						<button class="<?php echo $buttonclass; ?> m-1" data-filter=".<?php echo $tag->alias; ?>">
							<?php echo $tag->title; ?>
						</button>
						<?php
					}
				}
				?>
			</div>
			<?php
			echo '</div>';
		}

		/* show the fieldnames as headlines */
		foreach ($uniquefields as $id => $usedfield) {
			if (!empty($fieldvalues[$usedfield->name])) {
				echo '<div class="ui-group my-3">';
				if ($showfilterheadlines == '1') :
					echo '<h3 class="parent">' . ucwords($usedfield->name) . '</h3>';
				endif;
				?>
				<div class="button-group filters-button-group" data-filter-group="<?php echo replacechars($usedfield->name); ?>">
				<button class="<?php echo $buttonclass; ?> <?php echo $checkedclass; ?> m-1"
				        data-filter=""><?php echo JText::_('CCC_FILTER_SHOW_ALL'); ?></button>
				<?php
			}

			/* show the fieldvalues below their headlines */
			foreach ($fieldvalues as $name => $myfields) {
				if ($name == $usedfield->name) {
					foreach ($myfields as $myfield) { ?>
						<button class="<?php echo $buttonclass; ?> m-1" data-filter=".<?php echo specialchars($myfield); ?>">
							<?php echo $myfield; ?>
						</button>
						<?php
					}
				}
			} ?>
			</div>
			<?php
			echo '</div>';

		}
		?>

	</div>

<?php endif; ?>

<div class="<?php echo $gridcontainer; ?>">
	<div class="row no-gutters grid mt-5">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>

		<div class="grid-sizer <?php echo $mediumclass; ?>"></div>
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