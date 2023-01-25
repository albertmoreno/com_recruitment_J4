<?php
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Recruitment\Component\Recruitment\Site\Helper\RecruitmentHelper;

?>

<form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save_eligibility'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

    <!--div>
        <?php //echo Text::_('COM_RECRUITMENT_ELIGIBILITY'); 
        ?>
    </div-->
    <div class="control-group">
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="checkbox" name="eligibility1" value="1" class="form-control required uk-checkbox" <?php echo ($this->application->eligibility1) ? 'checked' : ''; ?> />
                <i><?php echo JText::_('COM_RECRUITMENT_ELIGIBILITY1'); ?></i></br></br>
            <?php else : ?>
                <?php if ($this->application->eligibility1) : ?>
                    <span style="color:green" uk-icon="icon: check"></span>
                    <i><?php echo JText::_('COM_RECRUITMENT_ELIGIBILITY1'); ?></i></br></br>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="checkbox" name="eligibility2" value="1" class="form-control required uk-checkbox" <?php echo ($this->application->eligibility2) ? 'checked' : ''; ?> />
                <i><?php echo JText::_('COM_RECRUITMENT_ELIGIBILITY2'); ?></i></br></br>
            <?php else : ?>
                <?php if ($this->application->eligibility2) : ?>
                    <span style="color:green" uk-icon="icon: check"></span>
                    <i><?php echo JText::_('COM_RECRUITMENT_ELIGIBILITY2'); ?></i></br></br>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($this->rights == 'write') : ?>
        <div class="control-group">
            <div class="controls">

                <?php if ($this->canSave) : ?>
                    <button type="submit" class="validate btn btn-primary uk-width-1-1">
                        <span class="fas fa-check" aria-hidden="true"></span>
                        <?php echo Text::_('JSUBMIT'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <input type="hidden" name="id" value="<?php echo $this->application->id; ?>" />
    <input type="hidden" name="job_id" value="<?php echo $this->job_id; ?>" />
    <input type="hidden" name="tab_id" value="1" />

    <input type="hidden" name="option" value="com_recruitment" />
    <input type="hidden" name="task" value="applicationform.save_eligibility" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>