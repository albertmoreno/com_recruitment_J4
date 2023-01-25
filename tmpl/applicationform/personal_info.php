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

<form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

    <div>
        <?php echo Text::_('COM_RECRUITMENT_INTRO_PERSONAL'); ?>
    </div>
    <div class="control-group">
        <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                Firstname</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="text" name="firstname" id="firstname" value="<?php echo $this->application->firstname ?>" class="form-control valid form-control-success" placeholder="Firstname" aria-invalid="false">
            <?php else : ?>
                <?php echo $this->application->firstname ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            Lastname</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="text" name="lastname" id="lastname" value="<?php echo $this->application->lastname ?>" class="form-control valid form-control-success" placeholder="Lastname" aria-invalid="false">
            <?php else : ?>
                <?php echo $this->application->lastname ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            Birth Date</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <?php echo JHTML::_('calendar', $this->application->birth_date, 'birth_date', 'birth_date', '%Y-%m-%d', array('size' => '8', 'maxlength' => '10', 'class' => ' validate required')); ?>
            <?php else : ?>
                <?php echo $this->application->birth_date ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            Birth Country</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <?php echo $this->lists['birth_country']; ?>
            <?php else : ?>
                <?php echo $this->country_name ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            Gender</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <?php echo $this->lists['genders']; ?>
            <?php else : ?>
                <?php echo $this->gender_name ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                Passport</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="text" name="passport" id="passport" value="<?php echo $this->application->passport ?>" class="form-control valid form-control-success" placeholder="Passport" aria-invalid="false">
            <?php else : ?>
                <?php echo $this->application->passport ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                Email</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="text" name="email" id="email" value="<?php echo $this->application->email ?>" class="form-control valid form-control-success" placeholder="Email" aria-invalid="false">
            <?php else : ?>
                <?php echo $this->application->email ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                Telephone</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <input type="text" name="telephone" id="telephone" value="<?php echo $this->application->telephone ?>" class="form-control valid form-control-success" placeholder="Telephone" aria-invalid="false">
            <?php else : ?>
                <?php echo $this->application->telephone ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            Wheredidu</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') : ?>
                <?php echo $this->lists['wheredidu']; ?>
            <?php else : ?>
                <?php echo $this->wheredidu_name ?>
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
    <input type="hidden" name="task" value="applicationform.save" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>