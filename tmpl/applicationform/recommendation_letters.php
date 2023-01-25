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

<?php if (count($this->referees) > 0) : ?>
    <h3><?php echo JText::_('COM_RECRUITMENT_RECOMMENDATION_LETTERS_LIST'); ?></h3>
    <table class="uk-table uk-table-striped">
        <thead>
            <tr>
                <td><?php echo JText::_('NAME'); ?></td>
                <td><?php echo JText::_('LASTNAME'); ?></td>
                <td><?php echo JText::_('INSTITUTION'); ?></td>
                <td><?php echo JText::_('EMAIL'); ?></td>
                <td><?php echo JText::_('SENT_EMAIL'); ?></td>
                <?php if ($this->is_manager) : ?>
                    <td><?php echo JText::_('FILENAME'); ?></td>
                    <td><?php echo JText::_('UPLOAD_DATE'); ?></td>
                <?php endif; ?>
                <?php if ($this->rights == 'write') : ?>
                    <td></td>
                <?php endif; ?>
                <?php if ($this->is_manager) : ?>
                    <td>
                    </td>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->referees as $referee) : ?>
                <tr>
                    <td><?php echo $referee->firstname; ?></td>
                    <td><?php echo $referee->lastname; ?></td>
                    <td><?php echo $referee->institution; ?></td>
                    <td><?php echo $referee->email; ?></td>
                    <td><?php echo $referee->sent_email; ?></td>
                    <?php if ($this->is_manager) : ?>
                        <td>
                            <?php if ($referee->filename) : ?>
                                <?php echo $referee->filename; ?>
                                <a href='<?php echo $_SERVER['PHP_SELF'];
                                            ?>?option=com_recruitment&task=application.download_file&app_id=<?php echo $this->application->id; ?>&filename=<?php echo $referee->filename; ?>' target="_blank" class="uk-icon-link" uk-icon="icon: download"></a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $referee->upload_date; ?></td>
                    <?php endif; ?>
                    <?php if ($this->rights == 'write') : ?>
                        <td>
                            <form action="<?php echo (JRoute::_("index.php")); ?>" method="post" name="delRefereeForm" id="delRefereeForm" class="form-validate">
                                <input type="hidden" name="option" value="com_recruitment" />
                                <input type="hidden" name="view" value="applicationform" />
                                <input type="hidden" name="task" value="applicationform.del_referee" />
                                <input type="hidden" name="referee_id" value="<?php echo $referee->id; ?>" />
                                <input type="hidden" name="app_id" value="<?php echo $this->application->id; ?>" />
                                <?php echo JHTML::_('form.token'); ?>
                                <button type="submit" style="background:none;border:0px" class="no-border" onClick="return confirm('Are you sure you want to delete this referee?');">
                                    <span uk-icon="icon: trash;"></span>
                                </button>
                            </form>
                        </td>
                        <?php if ($this->is_manager) : ?>
                            <td>
                                <?php
                                $upload_link = "https://services.icmab.es/recruitment/referee?upload_code=" . $referee->upload_code;
                                //$upload_link = JRoute::_('index.php?option=com_recruitment&view=referee&layout=default&upload_code='.$referee->upload_code); 
                                ?>
                                <a href='<?php echo $upload_link; ?>' target="_blank" class="uk-icon-link" uk-icon="icon: link"></a>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    </br>
<?php endif; ?>

<div>
    <?php echo Text::_('COM_RECRUITMENT_RECOMMENDATION_LETTERS_INTRO'); ?>
</div>

<?php if ($this->rights == 'write') : ?>

    <form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save_referee'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

        <div>
            <h3><?php echo Text::_('COM_RECRUITMENT_RECOMMENDATION_LETTERS_ADD'); ?></h3>
        </div>
        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Firstname</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="firstname" id="firstname" class="form-control valid form-control-success" placeholder="Firstname" aria-invalid="false">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Lastname</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="lastname" id="lastname" class="form-control valid form-control-success" placeholder="Lastname" aria-invalid="false">
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
                    Email</label>
            </div>
            <div class="controls has-success">
                <input type="text" name="email" id="email" class="form-control valid form-control-success" placeholder="Email" aria-invalid="false">
            </div>
        </div>

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

        <input type="hidden" name="id" value="<?php echo $this->application->id; ?>" />
        <input type="hidden" name="job_id" value="<?php echo $this->job_id; ?>" />
        <input type="hidden" name="tab_id" value="5" />

        <input type="hidden" name="option" value="com_recruitment" />
        <input type="hidden" name="task" value="applicationform.save_referee" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
<?php endif; ?>