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
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_recruitment');

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_recruitment'))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_USER_ID'); ?></th>
			<td><?php echo $this->item->user_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_JOB_ID'); ?></th>
			<td><?php echo $this->item->job_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_FIRSTNAME'); ?></th>
			<td><?php echo $this->item->firstname; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_LASTNAME'); ?></th>
			<td><?php echo $this->item->lastname; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_BIRTH_DATE'); ?></th>
			<td><?php echo $this->item->birth_date; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_GENDER_ID'); ?></th>
			<td><?php echo $this->item->gender_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_BIRTH_COUNTRY_ID'); ?></th>
			<td><?php echo $this->item->birth_country_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_PASSPORT'); ?></th>
			<td><?php echo $this->item->passport; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EMAIL'); ?></th>
			<td><?php echo $this->item->email; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_TELEPHONE'); ?></th>
			<td><?php echo $this->item->telephone; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_WHEREDIDU_ID'); ?></th>
			<td><?php echo $this->item->wheredidu_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_RECRUITMENT_COMMENTS'); ?></th>
			<td><?php echo $this->item->recruitment_comments; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_APPLICANT_CONTACTED_DATE'); ?></th>
			<td><?php echo $this->item->applicant_contacted_date; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_APPLICANT_CONTACTED'); ?></th>
			<td><?php echo $this->item->applicant_contacted; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_LINKEDIN_PUBLIC_LINK'); ?></th>
			<td><?php echo $this->item->linkedin_public_link; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_ELIGIBILITY1'); ?></th>
			<td><?php echo $this->item->eligibility1; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_ELIGIBILITY2'); ?></th>
			<td><?php echo $this->item->eligibility2; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION1'); ?></th>
			<td><?php echo $this->item->evaluation1; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION2'); ?></th>
			<td><?php echo $this->item->evaluation2; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION3'); ?></th>
			<td><?php echo $this->item->evaluation3; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION4'); ?></th>
			<td><?php echo $this->item->evaluation4; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION1_COMMENT'); ?></th>
			<td><?php echo $this->item->evaluation1_comment; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION2_COMMENT'); ?></th>
			<td><?php echo $this->item->evaluation2_comment; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION3_COMMENT'); ?></th>
			<td><?php echo $this->item->evaluation3_comment; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_EVALUATION4_COMMENT'); ?></th>
			<td><?php echo $this->item->evaluation4_comment; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_SUBMIT_DATE'); ?></th>
			<td><?php echo $this->item->submit_date; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RECRUITMENT_FORM_LBL_APPLICATION_STATUS_ID'); ?></th>
			<td><?php echo $this->item->status_id; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_recruitment&task=application.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_RECRUITMENT_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_recruitment.application.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_RECRUITMENT_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_RECRUITMENT_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_recruitment&task=application.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_RECRUITMENT_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_RECRUITMENT_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>