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

JHtml::_('jquery.framework');
JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.4/isotope.pkgd.min.js', array('version' => 'auto', 'relative' => true), array('defer' => true, 'async' => false));
JHtml::_('script', 'https://imagesloaded.desandro.com/imagesloaded.pkgd.js', array('version' => 'auto', 'relative' => true), array('defer' => true, 'async' => false));
$doc->addStyleDeclaration('.is-checked {background: #000;}');
function specialchars($string)
{
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´", "/", "-", " ", ",", "_");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", " ", " ", " ", " ", " ", " ");
	return strtolower(str_replace($search, $replace, $string));
}

?>


<?php
$uniquetag = array();
$uniquepaths = array();
$parentnames = array();
$uniquefields = array();

foreach ($this->intro_items as $item) {
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


	if ($this->params->get('customfieldsfilter', '1')) :
		/* get the fields and push their value into an array */

		$fields = $item->jcfields;

		foreach ($fields as $field) {
			$uniquefields[$field->name] = $field;
			$fieldvalues[$field->name][] = array();
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


<?php

/* show the tagparents as headlines */
foreach ($parentnames

         as $parentname) {
	echo '<h3 class="parent my-3">' . ucwords($parentname) . '</h3>';
	?>
	<div class="button-group filters-button-group">
		<button class="btn btn-primary is-checked"
		        data-filter="*"><?php echo JText::_('CCC_FILTER_SHOW_ALL'); ?></button>
		<?php
		foreach ($uniquetag as $id => $tag) {
			/* put the tags below their parents */
			list($parent, $child) = explode("/", $tag->path);
			if ($parentname == $parent) { ?>
				<button class="btn btn-primary" data-filter=".<?php echo $tag->alias; ?>">
					<?php echo $tag->title; ?>
				</button>
				<?php
			}
		}
		?>
	</div>
	<?php
}

/* show the fieldnames as headlines */
foreach ($uniquefields

         as $id => $usedfield) {
	if (!empty($fieldvalues[$usedfield->name])) {
		echo '<h3 class="parent my-3">' . $usedfield->name . '</h3>';
		?>
		<div class="button-group filters-button-group">
		<button class="btn btn-primary is-checked"
		        data-filter="*"><?php echo JText::_('CCC_FILTER_SHOW_ALL'); ?></button>
		<?php
	}

	/* show the fieldvalues below their headlines */
	foreach ($fieldvalues as $name => $myfields) {
		if ($name == $usedfield->name) {
			foreach ($myfields as $myfield) { ?>
				<button class="btn btn-primary" data-filter=".<?php echo specialchars($myfield); ?>">
					<?php echo $myfield; ?>
				</button>
				<?php
			}
		}
	} ?>
	</div>
	<?php
}
?>



<div class="grid mt-5">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

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

	<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>


<script>
	jQuery(document).ready(function () {

		// init Isotope
		var $grid = jQuery('.grid').isotope({
			itemSelector: '.element-item',
			layoutMode: 'masonry'
		});

		jQuery('.grid').imagesLoaded().progress(function () {
			jQuery('.grid').isotope('layout');
		});

		// filter functions
		var filterFns = {
			// show if number is greater than 50
			numberGreaterThan50: function () {
				var number = $(this).find('.number').text();
				return parseInt(number, 10) > 50;
			},
			// show if name ends with -ium
			ium: function () {
				var name = $(this).find('.name').text();
				return name.match(/ium$/);
			}
		};

		// bind filter button click
		jQuery('.filters-button-group').on('click', 'button', function () {
			var filterValue = jQuery(this).attr('data-filter');
			// use filterFn if matches value
			filterValue = filterFns[filterValue] || filterValue;
			$grid.isotope({filter: filterValue});
		});
		// change is-checked class on buttons
		jQuery('.button-group').each(function (i, buttonGroup) {
			var $buttonGroup = jQuery(buttonGroup);
			$buttonGroup.on('click', 'button', function () {
				$buttonGroup.find('.is-checked').removeClass('is-checked');
				jQuery(this).addClass('is-checked');
			});
		});
	});
</script>