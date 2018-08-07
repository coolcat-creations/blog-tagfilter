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


		if ($showfieldfilter) :
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

<div class="container">
	<div class="row filters">

		<?php

		/* show the tagparents as headlines */
		foreach ($parentnames as $parentname) {
			echo '<div class="ui-group my-3 mx-auto ' . $smallclass . '">';
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
</div>
