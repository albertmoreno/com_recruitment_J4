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

<form id="form-application" action="<?php echo Route::_('index.php?option=com_recruitment&task=applicationform.save_programmes'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

    <div class="control-group">
        <div class="control-label">
            First Choice</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') :
                echo $this->lists['programmes_1'];
            else :
                echo $this->selectedprogrammestodisplay[0];
            endif; ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            Second Choice</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') :
                echo $this->lists['programmes_2'];
            else :
                echo $this->selectedprogrammestodisplay[1];
            endif; ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            Third Choice</label>
        </div>
        <div class="controls has-success">
            <?php if ($this->rights == 'write') :
                echo $this->lists['programmes_3'];
            else :
                echo $this->selectedprogrammestodisplay[2];
            endif; ?>
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
    <input type="hidden" name="tab_id" value="6" />

    <input type="hidden" name="option" value="com_recruitment" />
    <input type="hidden" name="task" value="applicationform.save_programmes" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>