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

?>

<div class="uk-grid">
	<div class="uk-width-1-1">
		<?php echo JText::_('TEXT_REFEREE_UPLOAD_FILE'); ?>
		</br>
		<hr>
		</br>
	</div>
</div>
<h3><?php echo JText::_('REFEREE_PAGE'); ?></h3>

<?php if (empty($this->referee->id) || (!empty($this->referee->filename))) : ?>
	<h3><?php echo JText::_('RIEN_A_VOIR'); ?></h3>
<?php else : ?>
	<h3><?php echo JText::_('ADD_A_NEW_FILE_FOR'); ?> - <?php echo $this->job->description; ?> -
		to <?php echo $this->application->lastname . ', ' . $this->application->firstname; ?></h3>
	<!--h4><?php echo $this->application->lastname . ', ' . $this->application->firstname; ?></h4--></br>
	<form action="<?php echo (JRoute::_("index.php")); ?>" method="post" name="RefereeFileForm" id="RefereeFileForm" class="form-validate" enctype="multipart/form-data">
		<div class="uk-grid">
			<div class="uk-width-1-3">
				<?php echo JText::_('UPLOAD_FILE'); ?>
			</div>
			<div class="uk-width-2-3">
				<input type='hidden' name='MAX_FILE_SIZE' value='4194304' />
				<input type='file' class='form-control required inputbox' name='uploaded_file' />
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<button class="validate btn btn-primary btn-lg btn-block" name="save" value="true" type="submit"><?php echo JText::_('UPLOAD_FILE'); ?></button>
				<input type="hidden" name="option" value="com_recruitment" />
				<input type="hidden" name="view" value="applicationform" />
				<input type="hidden" name="task" value="applicationform.save_referee_file" />
				<?php echo JHTML::_('form.token'); ?>
				<input type="hidden" name="upload_code" value="<?php echo $this->referee->upload_code; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->referee->id; ?>" />
				<input type="hidden" name="application_id" value="<?php echo $this->referee->application_id; ?>" />
			</div>
		</div>
	</form>


<?php endif; ?>