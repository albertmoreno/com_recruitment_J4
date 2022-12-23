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
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_RECRUITMENT_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_RECRUITMENT_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-application"
			  action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'application')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'application', Text::_('COM_RECRUITMENT_TAB_APPLICATION', true)); ?>
	<?php echo $this->form->renderField('user_id'); ?>

	<?php echo $this->form->renderField('job_id'); ?>

	<?php echo $this->form->renderField('firstname'); ?>

	<?php echo $this->form->renderField('lastname'); ?>

	<?php echo $this->form->renderField('birth_date'); ?>

	<?php echo $this->form->renderField('gender_id'); ?>

	<?php echo $this->form->renderField('birth_country_id'); ?>

	<?php echo $this->form->renderField('passport'); ?>

	<?php echo $this->form->renderField('email'); ?>

	<?php echo $this->form->renderField('telephone'); ?>

	<?php echo $this->form->renderField('wheredidu_id'); ?>

	<?php echo $this->form->renderField('recruitment_comments'); ?>

	<?php echo $this->form->renderField('applicant_contacted_date'); ?>

	<?php echo $this->form->renderField('applicant_contacted'); ?>

	<?php echo $this->form->renderField('linkedin_public_link'); ?>

	<?php echo $this->form->renderField('eligibility1'); ?>

	<?php echo $this->form->renderField('eligibility2'); ?>

	<?php echo $this->form->renderField('evaluation1'); ?>

	<?php echo $this->form->renderField('evaluation2'); ?>

	<?php echo $this->form->renderField('evaluation3'); ?>

	<?php echo $this->form->renderField('evaluation4'); ?>

	<?php echo $this->form->renderField('evaluation1_comment'); ?>

	<?php echo $this->form->renderField('evaluation2_comment'); ?>

	<?php echo $this->form->renderField('evaluation3_comment'); ?>

	<?php echo $this->form->renderField('evaluation4_comment'); ?>

	<?php echo $this->form->renderField('submit_date'); ?>

	<?php echo $this->form->renderField('status_id'); ?>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_recruitment"/>
			<input type="hidden" name="task"
				   value="applicationform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
