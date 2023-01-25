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

<?php if (count($this->docs) > 0) : ?>
    <h3><?php echo JText::_('FILES'); ?></h3>
    <table class="uk-table uk-table-striped">
        <thead>
            <tr>
                <td><?php echo JText::_('DOC_TYPE'); ?></td>
                <td><?php echo JText::_('FILENAME'); ?></td>
                <td><?php echo JText::_('DESCRIPTION'); ?></td>
                <td><?php echo JText::_('UPLOAD_DATE'); ?></td>
                <?php if ($this->rights == 'write') : ?>
                    <td></td>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->docs as $file) : ?>
                <tr>
                    <td><?php echo $file->description; ?></td>
                    <td><?php echo $file->filename; ?>
                        <!--a href='<?php echo $_SERVER['PHP_SELF'];
                                    ?>?option=com_recruitment&task=application.download_file&app_id=<?php echo $this->application->id; ?>&file_id=<?php echo $file->id; ?>'
                       target="_blank" class="uk-icon-link" uk-icon="icon: donload"></a-->

                        <a href='<?php echo $_SERVER['PHP_SELF'];
                                    ?>?option=com_recruitment&task=applicationform.download_file&app_id=<?php echo $this->application->id; ?>&filename=<?php echo $file->filename; ?>' target="_blank" class="uk-icon-link" uk-icon="icon: download"></a>
                    </td>
                    <td><?php echo $file->description; ?></td>
                    <td><?php echo $file->creation_date; ?></td>
                    <?php if ($this->rights == 'write') : ?>
                        <td>
                            <form action="<?php echo (JRoute::_("index.php")); ?>" method="post" name="delFileForm" id="delFileForm" class="form-validate">
                                <input type="hidden" name="option" value="com_recruitment" />
                                <input type="hidden" name="view" value="application" />
                                <input type="hidden" name="task" value="applicationform.del_file" />
                                <input type="hidden" name="file_id" value="<?php echo $file->id; ?>" />
                                <input type="hidden" name="app_id" value="<?php echo $this->application->id; ?>" />
                                <?php echo JHTML::_('form.token'); ?>
                                <button type="submit" style="background:none;border:0px" class="no-border" onClick="return confirm('Are you sure you want to delete this file?');">
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
    <?php echo Text::_('COM_RECRUITMENT_SUPPORTING_DOCS_INTRO'); ?>
</div>

<?php if ($this->rights == 'write') : ?>
    <form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save_docs'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

        <div>
            <h3><?php echo Text::_('COM_RECRUITMENT_SUPPORTING_DOCS_ADD'); ?></h3>
        </div>

        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Document Type</label>
            </div>
            <div class="controls has-success">
                <?php echo $this->lists['doc_types']; ?>
            </div>
        </div>

        <div class="control-group">
            <div class="control-label"><label id="jform_firstname-lbl" for="jform_firstname">
                    Upload File</label>
            </div>
            <div class="controls has-success">
                <input type='hidden' name='MAX_FILE_SIZE' value='4194304' />
                <input type='file' class='form-control required inputbox' name='uploaded_file' />
            </div>
        </div>

        <div class="control-group">
            <div class="control-label">
                Description</label>
            </div>
            <div class="controls has-success">
                <textarea class="uk-textarea" name="description" id="description" rows="5" placeholder="Textarea" aria-label="Textarea"></textarea>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">

                <?php if ($this->canSave) : ?>
                    <button type="submit" class="validate btn btn-primary uk-width-1-1">
                        <span class="fas fa-check" aria-hidden="true"></span>
                        <?php echo Text::_('SUBMIT_DOC'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $this->application->id; ?>" />
        <input type="hidden" name="job_id" value="<?php echo $this->job_id; ?>" />
        <input type="hidden" name="tab_id" value="2" />

        <input type="hidden" name="option" value="com_recruitment" />
        <input type="hidden" name="task" value="applicationform.save_docs" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
<?php endif; ?>