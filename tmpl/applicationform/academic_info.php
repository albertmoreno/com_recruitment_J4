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

<?php if (count($this->degrees) > 0) : ?>
    <h3><?php echo JText::_('DEGREES'); ?></h3>
    <table class="uk-table uk-table-striped">
        <thead>
            <tr>
                <td><?php echo JText::_('TYPE'); ?></td>
                <td><?php echo JText::_('DEGREE'); ?></td>
                <!--td><?php //echo JText::_('UNIVERSITY'); 
                        ?></td-->
                <td><?php echo JText::_('INSTITUTION'); ?></td>
                <!--td><?php //echo JText::_('DIRECTOR_NAME'); 
                        ?></td-->
                <?php if ($this->rights == 'write') : ?>
                    <td></td>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->degrees as $degree) : ?>
                <tr>
                    <td><?php echo $degree->type; ?></td>
                    <td><?php echo $degree->degree; ?><br>
                        <small><?php echo $degree->final_mark; ?></small>
                    </td>
                    <!--td><?php //echo $degree->university; 
                            ?><br>
                    <small><?php //echo $degree->country; 
                            ?></small>
                </td-->
                    <td><?php echo $degree->institution; ?><br>
                        <small><?php echo $degree->printable_name; ?></small><br>
                        <small><?php //echo $degree->start_date; 
                                ?><?php echo $degree->end_date; ?></small>
                    </td>
                    <!--td><?php //echo $degree->director_name; 
                            ?></td-->
                    <?php if ($this->rights == 'write') : ?>
                        <td>
                            <form action="<?php echo (JRoute::_("index.php")); ?>" method="post" name="delAcademicForm" id="delAcademicForm" class="form-validate">
                                <input type="hidden" name="option" value="com_recruitment" />
                                <input type="hidden" name="view" value="applicationform" />
                                <input type="hidden" name="task" value="applicationform.del_academic_data" />
                                <input type="hidden" name="academic_data_id" value="<?php echo $degree->id; ?>" />
                                <input type="hidden" name="app_id" value="<?php echo $this->application->id; ?>" />
                                <?php echo JHTML::_('form.token'); ?>
                                <button type="submit" style="background:none;border:0px" class="no-border" onClick="return confirm('Are you sure you want to delete this academic data?');">
                                    <span uk-icon="icon: trash;"></span>
                                </button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    </br>
<?php endif; ?>

<div>
    <?php echo Text::_('COM_RECRUITMENT_INTRO_ACADEMIC'); ?>
</div>

<?php if ($this->rights == 'write') : ?>

    <form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save_academic'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
        <div>
            <h3><?php echo Text::_('COM_RECRUITMENT_ACADEMIC_ADD'); ?></h3>
        </div>
        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Type</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="type" id="type" class="form-control valid form-control-success" placeholder="Type" aria-invalid="false">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                Degree</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="degree" id="degree" class="form-control valid form-control-success" placeholder="Degree" aria-invalid="false">
            </div>
        </div>

        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    University / Institution</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="institution" id="institution" class="form-control valid form-control-success" placeholder="University / Institution" aria-invalid="false">
            </div>
        </div>

        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Country</label>
            </div>
            <div class="controls has-success">
                <?php echo $this->lists['countries']; ?>
            </div>
        </div>

        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Final Mark</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="final_mark" id="final_mark" class="form-control valid form-control-success" placeholder="Final Mark" aria-invalid="false">
            </div>
        </div>

        <div class="control-group">
            <div class="control-label">
                End Date</label>
            </div>
            <div class="controls has-success">
                <?php echo JHTML::_('calendar', '', 'end_date', 'end_date', '%Y-%m-%d', array('size' => '8', 'maxlength' => '10', 'class' => ' validate required')); ?>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">

                <?php if ($this->canSave) : ?>
                    <button type="submit" class="validate btn btn-primary uk-width-1-1">
                        <span class="fas fa-check" aria-hidden="true"></span>
                        <?php echo Text::_('SUBMIT_ACADEMIC'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $this->application->id; ?>" />
        <input type="hidden" name="job_id" value="<?php echo $this->job_id; ?>" />
        <input type="hidden" name="tab_id" value="2" />

        <input type="hidden" name="option" value="com_recruitment" />
        <input type="hidden" name="task" value="applicationform.save_academic" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
<?php endif; ?>