<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Recruitment
 * @author     Albert Moreno <albert.moreno.forrellad@gmail.com>
 * @copyright  2022 Albert Moreno
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Recruitment\Component\Recruitment\Site\Helper\RecruitmentHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_recruitment', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = RecruitmentHelper::canUserEdit($this->item, $user);

?>

<div class="application-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new \Exception(Text::_('COM_RECRUITMENT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<h2>
			<?php if ($this->application->firstname) :
				echo $this->application->lastname . ', ' . $this->application->firstname . ' - ' . $this->job->short_description . ' ( ID ' . $this->application->id . ' )';
			else :
				echo $this->job->short_description;
			endif; ?>
		</h2>

		<ul class="uk-tab" data-uk-tab="{connect:'#my-id'}">
			<li class="<?php echo ($this->tab_id == 1) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_PERSONAL_INFO'); ?></a></li>
			<li class="<?php echo ($this->tab_id == 2) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_ACADEMIC_INFO'); ?></a></li>
			<li class="<?php echo ($this->tab_id == 3) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_ELIGIBILITY'); ?></a></li>
			<li class="<?php echo ($this->tab_id == 4) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_SUPPORTING_DOCS'); ?></a></li>
			<li class="<?php echo ($this->tab_id == 5) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_RECOMMENDATION_LETTERS'); ?></a></li>
			<li class="<?php echo ($this->tab_id == 6) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_RESEARCH_OPTIONS'); ?></a></li>
			<li class="<?php echo ($this->tab_id == 7) ? 'uk-active' : ''; ?>"><a href=""><?php echo Text::_('COM_RECRUITMENT_STATUS'); ?></a></li>
		</ul>
		<ul id="my-id" class="uk-switcher uk-margin">
			<li>
				<?php require_once('personal_info.php'); ?>
			</li>
			<li>
				<?php require_once('academic_info.php'); ?>
			</li>
			<li>
				<?php require_once('eligibility.php'); ?>
			</li>
			<li>
				<?php require_once('supporting_docs.php'); ?>
			</li>
			<li>
				<?php require_once('recommendation_letters.php'); ?>
			</li>
			<li>
				<?php require_once('research_options.php'); ?>
			</li>
			<li>
				<?php require_once('status.php'); ?>
			</li>
		</ul>

	<?php endif; ?>
</div>