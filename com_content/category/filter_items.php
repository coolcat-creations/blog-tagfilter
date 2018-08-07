<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.core');
JHtml::_('formbehavior.chosen', 'select');
$doc = Factory::getDocument();
$user = Factory::getUser();

$this->cssframework = $this->params->get('cssframework', 'bs4');
$readmore = $this->params->get('read_more', '1');
$showlinks = $this->params->get('showlinks', '1');
$tagscontent = $this->params->get('tagscontent', '1');
$introtextpos = $this->params->get('introtextposition', 'overlay');
$titlepos = $this->params->get('titleposition', 'overlay');

$params = $this->item->params;

if ($this->cssframework == 'bs4') {
	$mediumclass = 'col-lg-' . $this->gridcols  . ' col-md-' . $this->gridcols  . ' col-sm-6';
	$smallclass = 'col-12';
}


if ($this->cssframework == 'bs3') {
	$mediumclass = 'col-md-' . $this->gridcols  . ' col-sm-6';
	$smallclass = 'col-xs-12';
}

if ($this->cssframework == 'bs2') {
	$mediumclass = 'span' . $this->gridcols  . ' span6';
}

if ($this->cssframework == 'none') {
	$mediumclass = '';
	$smallclass = '';
}


if ($this->excludefields) {
	$this->excludefields = explode(",", $this->excludefields);
}

$tags = $this->item->tags->itemTags;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);

$filters = "";
foreach ($tags as $tag) {
	$filters .= ' ' . $tag->alias . ' ';
}
foreach ($this->item->jcfields as $jcfield) {
	if (!in_array($jcfield->id, $this->excludefields)) {
		$filters .= ' ' . specialchars($jcfield->value) . ' ';
	}
}

if ($this->introimagemode == 'bg') {
	$introstyle = 'style="background-image:url(\'' . $images->image_intro . '\'); background-size:cover;"';
}

if ($this->fullimagemode == 'bg') {
	$fullstyle = 'style="background-image:url(\'' . $images->image_fulltext . '\'); background-size:cover;"';
}


Factory::getDocument()->addScriptDeclaration("
		var resetFilter = function() {
		document.getElementById('filter-search').value = '';
	}
");
?>


<div class="element-item <?php echo $smallclass . ' ' . $mediumclass; ?> <?php echo $filters; ?> ">

	<div class="grid-item-content square">

		<div class="content" <?php echo $introstyle; ?>>
			<div class="standard">

				<?php if ($titlepos == "standard" || $titlepos == "both") { ?>

					<div class="<?php echo $smallclass; ?> text-center mt-1">

						<?php echo LayoutHelper::render('joomla.content.filtertitle', $this->item); ?>

						<?php if ($this->item->event->afterDisplayTitle && $this->afterdisplaytitle) { ?>
							<?php echo $this->item->event->afterDisplayTitle; ?>
						<?php } ?>
					</div>
				<?php } ?>


				<?php if ($this->item->introtext && ($introtextpos == "standard" || $introtextpos == "both")) { ?>
					<div class="<?php echo $smallclass; ?> text-center">
						<?php echo $this->item->introtext; ?>
					</div>
				<?php } ?>
			</div>


			<div class="overlay" <?php echo $fullstyle; ?>>
				<div>
					<div class="row p-3 text">

						<?php if ($titlepos == "overlay" || $titlepos == "both") { ?>

							<div class="<?php echo $smallclass; ?> text-center">
								<?php echo LayoutHelper::render('joomla.content.filtertitle', $this->item); ?>

								<?php if ($this->item->event->afterDisplayTitle && $this->afterdisplaytitle) { ?>
									<?php echo $this->item->event->afterDisplayTitle; ?>
								<?php } ?>
							</div>
						<?php } ?>

						<?php if ($this->item->event->beforeDisplayContent && $this->beforedisplaycontent) { ?>
							<div class="<?php echo $smallclass; ?>" text-center>
								<?php echo $this->item->event->beforeDisplayContent; ?>
							</div>
						<?php } ?>

						<?php if ($this->introimagemode == 'content') {
							echo '<div class="' . $smallclass . ' text-center">';
							echo LayoutHelper::render('joomla.content.intro_image', $this->item);
							echo '</div>';
						}
						?>

						<?php if ($this->fullimagemode == 'content') {
							echo '<div class="' . $smallclass . ' text-center">';
							echo LayoutHelper::render('joomla.content.full_image', $this->item);
							echo '</div>';
						}
						?>

						<?php if ($this->item->introtext && ($introtextpos == "overlay" || $introtextpos == "both")) { ?>
							<div class="<?php echo $smallclass; ?> text-center">
								<?php echo $this->item->introtext; ?>
							</div>
						<?php } ?>

						<?php if ($this->item->event->afterDisplayContent && $this->afterdisplaycontent) { ?>
							<div class="<?php echo $smallclass; ?>">
								<?php echo $this->item->event->afterDisplayContent; ?>
							</div>
						<?php } ?>

						<?php if ($this->item->tags->itemTags && $tagscontent == '1') { ?>
							<div class="<?php echo $smallclass; ?>">
								<ul class="tags">
									<?php
									foreach ($this->item->tags->itemTags as $tag) {
										echo '<li itemprop="keywords">';
										echo $tag->title;
										echo '</li>';
									} ?>
								</ul>
							</div>
						<?php } ?>


						<?php if ($this->params->get('show_readmore') && $params->get('show_readmore') && $this->item->readmore) :
							if ($params->get('access-view')) :
								$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
							else :
								$menu = JFactory::getApplication()->getMenu();
								$active = $menu->getActive();
								$itemId = $active->id;
								$link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
								$link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
							endif; ?>
							<div class="<?php echo $smallclass; ?>">
								<?php echo JLayoutHelper::render('joomla.content.filterreadmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); ?>
							</div>
						<?php endif;

						if ($showlinks && $urls && (!empty($urls->urla) || !empty($urls->urlb) || !empty($urls->urlc))) :

							?>
							<div class="<?php echo $smallclass; ?>">
								<?php

								$urlarray = array(
									array($urls->urla, $urls->urlatext, $urls->targeta, 'a'),
									array($urls->urlb, $urls->urlbtext, $urls->targetb, 'b'),
									array($urls->urlc, $urls->urlctext, $urls->targetc, 'c')
								);
								foreach ($urlarray as $url) :
									$link = $url[0];
									$label = $url[1];
									$target = $url[2];
									$id = $url[3];

									if (!$link) :
										continue;
									endif;

									// If no label is present, take the link
									$label = $label ?: $link;

									// If no target is present, use the default
									$target = $target ?: $params->get('target' . $id);

									$linkclass = 'btn btn-outline-secondary m-1';

									if ($this->cssframework == 'bs4') {
										$targetidentifier = 'class="' . $linkclass . '" modaltarget-' . $id . '" data-toggle="modal"';
										?>

										<script>
											jQuery(document).ready(function () {

												jQuery('a.modaltarget-<?php echo $id;?>').click(function () {
													var src = jQuery(this).attr('data-source');
													jQuery("#iframe-modal-<?php echo $id;?>").attr('src', src);
												});
											})


										</script>

										<?php

									} else {
										$targetidentifier = 'rel="{handler: \'iframe\', size: {x:600, y:600}} noopener noreferrer" class="modal"';
									}

									?>

									<?php
									// Compute the correct link

									switch ($target) {
										case 1:
											// open in a new window
											echo '<a href="' . htmlspecialchars($link, ENT_COMPAT, 'UTF-8') . '" target="_blank"  rel="nofollow noopener noreferrer" class="' . $linkclass . '">' .
												htmlspecialchars($label, ENT_COMPAT, 'UTF-8') . '</a>';
											break;

										case 2:
											// open in a popup window
											$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=600';
											echo "<a href=\"" . htmlspecialchars($link, ENT_COMPAT, 'UTF-8') . "\" onclick=\"window.open(this.href, 'targetWindow', '" . $attribs . "'); return false;\" rel=\"noopener noreferrer\" class=\"' . $linkclass . '\" >" .
												htmlspecialchars($label, ENT_COMPAT, 'UTF-8') . '</a>';
											break;
										case 3:
											// open in a modal window
											JHtml::_('behavior.modal', 'a.modal');
											echo '<a ' . $targetidentifier . ' data-target="#modal-' . $id . '" data-source="' . $link . '" href="' . htmlspecialchars($link, ENT_COMPAT, 'UTF-8') . '"  >' .
												htmlspecialchars($label, ENT_COMPAT, 'UTF-8') . ' </a>';
											break;

										default:
											// open in parent window
											echo '<a href="' . htmlspecialchars($link, ENT_COMPAT, 'UTF-8') . '" rel="nofollow" class="' . $linkclass . '">' .
												htmlspecialchars($label, ENT_COMPAT, 'UTF-8') . ' </a>';
											break;
									}
									?>


								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>
				</div>

			</div>

		</div>
	</div>
</div>

<?php if ($showlinks && $urls && (!empty($urls->urla) || !empty($urls->urlb) || !empty($urls->urlc))) : ?>
	<?php foreach ($urlarray as $url) :
		$link = $url[0];
		$label = $url[1];
		$target = $url[2];
		$id = $url[3];

		if (!$link) :
			continue;
		endif;

// If no label is present, take the link
		$label = $label ?: $link;

// If no target is present, use the default
		$target = $target ?: $params->get('target' . $id);

		if ($this->cssframework == 'bs4') {
			$targetidentifier = 'data-toggle="modal"';
		} else {
			$targetidentifier = 'class="modal"';
		}

		?>
		<?php if ($this->cssframework == 'bs4') : ?>
		<div class="modal fade" id="modal-<?php echo $id; ?>" tabindex="-1" role="dialog"
		     aria-labelledby="modal-<?php echo $id; ?>" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><?php echo $label; ?></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<iframe id="iframe-modal-<?php echo $id; ?>" frameborder="0" scrolling="no"></iframe>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif;
	endforeach;
endif; ?>
