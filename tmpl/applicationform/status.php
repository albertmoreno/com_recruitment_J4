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


<div>
    <?php echo Text::_('COM_RECRUITMENT_STATUS_INTRO'); ?>
</div>
<hr>
<div>
    <h3><?php echo Text::_('COM_RECRUITMENT_CHECKLIST'); ?></h3>
</div>
<ul class="uk-list">
    <li>
        <?php if ($this->checklist->too_late) : ?>
            <?php echo JText::_('OFF_TIME'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php else : ?>
            <?php echo JText::_('ON_TIME'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->personal_data) : ?>
            <?php echo JText::_('PERSONAL_DATA_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('PERSONAL_DATA_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->degrees) : ?>
            <?php echo JText::_('DEGREES_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('DEGREES_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->cv) : ?>
            <?php echo JText::_('CV_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('CV_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->motivation_letter) : ?>
            <?php echo JText::_('MOTIVATION_LETTER_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('MOTIVATION_LETTER_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->academic_records) : ?>
            <?php echo JText::_('ACADEMIC_RECORDS_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('ACADEMIC_RECORDS_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->referees) : ?>
            <?php echo JText::_('REFEREES_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('REFEREES_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
    <li>
        <?php if ($this->checklist->selectedprogrammes) : ?>
            <?php echo JText::_('SELECTED_PROGRAMS_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('SELECTED_PROGRAMS_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>

    <li>
        <?php if ($this->checklist->eligibility) : ?>
            <?php echo JText::_('ELIGIBILITY_OK'); ?>
            <span style="color:green" uk-icon="icon: check"></span>
        <?php else : ?>
            <?php echo JText::_('ELIGIBILITY_MISSING'); ?>
            <span style="color:red" uk-icon="icon: close"></span>
        <?php endif; ?>
    </li>
</ul>
<hr>
<div>
    <h3><?php echo Text::_('COM_RECRUITMENT_CURRENT_STATUS'); ?></h3>
    <?php echo $this->actual_status->description; ?>
    <?php if ($this->application->recruitment_comments) : ?>
        <h3><?php echo Text::_('PRIVATE_NOTES'); ?></h3>
        <?php echo $this->application->recruitment_comments; ?>
    <?php endif; ?>
</div>

<?php if ($this->is_manager) : ?>
    <br>
    <hr><br>
    <form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.change_status'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

        <div class="uk-grid">
            <div class="uk-width-1-3">
                <?php echo JText::_('NEW_STATUS'); ?>
            </div>
            <div class="uk-width-2-3">
                <?php echo $this->lists['status']; ?>
            </div>
        </div>
        <div class="uk-grid">
            <div class="uk-width-1-3">
                <?php echo JText::_('SEND_EMAIL'); ?>
            </div>
            <div class="uk-width-2-3">
                <input type="checkbox" name="send_email" value="yes">
            </div>
        </div>
        <div class="uk-grid">
            <div class="uk-width-1-3">
                <?php echo JText::_('PRIVATE_NOTES'); ?>
            </div>
            <div class="uk-width-2-3">
                <textarea name="recruitment_comments" rows="6" class="form-control" /><?php echo $this->application->recruitment_comments; ?></textarea>
            </div>
        </div>
        <div class="uk-grid">
            <div class="uk-width-1-1">
                <?php if ($this->rights == 'write') : ?>
                    <button class="validate btn btn-primary btn-lg btn-block" name="save" value="true" type="submit"><?php echo JText::_('SUBMIT'); ?></button>
                    <input type="hidden" name="option" value="com_recruitment" />
                    <input type="hidden" name="view" value="applicationform" />
                    <input type="hidden" name="task" value="applicationform.change_status" />
                    <?php echo JHTML::_('form.token'); ?>
                <?php endif; ?>
                <input type="hidden" name="id" value="<?php echo $this->application->id; ?>" />
                <input type="hidden" name="job_id" value="<?php echo $this->job_id; ?>" />
                <input type="hidden" name="tab_id" value="7" />
            </div>
        </div>
    </form>
<?php else : ?>
    <div class="control-group">
        <div class="controls">
            <?php if ($this->rights == 'write') : ?>
                <form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.submit_application'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

                    <button type="submit" class="validate btn btn-primary uk-width-1-1">
                        <span class="fas fa-check" aria-hidden="true"></span>
                        <?php echo "Submit your Application"; ?>
                    </button>
                    <input type="hidden" name="id" value="<?php echo $this->application->id; ?>" />
                    <input type="hidden" name="job_id" value="<?php echo $this->job_id; ?>" />
                    <input type="hidden" name="tab_id" value="7" />

                    <input type="hidden" name="option" value="com_recruitment" />
                    <input type="hidden" name="task" value="applicationform.submit_application" />
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            <?php endif; ?>

        </div>
    </div>
<?php endif; ?>