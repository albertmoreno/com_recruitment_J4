<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Recruitment
 * @author     Albert Moreno <albert.moreno.forrellad@gmail.com>
 * @copyright  2022 Albert Moreno
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Recruitment\Component\Recruitment\Site\View\Applicationform;
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Recruitment\Component\Recruitment\Site\Helper\RecruitmentHelper;
use JHtml;

/**
 * View class for a list of Recruitment.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	protected $canSave;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->params  = $app->getParams('com_recruitment');
		$this->canSave = $this->get('CanSave');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new \Exception(implode("\n", $errors));
		}

		if ($user->id == 0) {
			echo "Please logon or provide a valid username";
			return;
		}

		$this->is_manager = RecruitmentHelper::isManager();

		//receive job id and $user
		$jinput = Factory::getApplication()->input;
		$this->job_id = $jinput->get('job_id', '3');
		$this->app_id = $id = $jinput->get('id');

		$session = Factory::getSession();
		$this->tab_id = $session->get('tab_id');

		$this->job = RecruitmentHelper::getJob($this->job_id);

		//print_r($jinput);

		if ($id) :
			$model = Factory::getApplication()->bootComponent('com_recruitment')->getMVCFactory()->createModel('Application', 'Site');
			$this->application = $model->getItem($this->app_id);
			$this->job_id = $this->application->job_id;
		else :
			$this->application = RecruitmentHelper::getApplication($this->job_id);
			$id = $this->application->id;
		endif;

		if ($this->is_manager) :
			$this->rights = 'write';
		else :
			if ($user->id != $this->application->user_id) :
				echo "Your are not allowed to access this application";
				return;
			endif;
			if ((!$id) || ($this->application->status_id == 1)) :
				$this->rights = 'write';
			else :
				$this->rights = 'read';
			endif;
		endif;

		$this->degrees = RecruitmentHelper::getDegrees($id);
		$this->docs = RecruitmentHelper::getDocs($id);
		$this->referees = RecruitmentHelper::getReferees($id);
		$selectedprogrammes = RecruitmentHelper::getSelectedProgrammes($id);
		$this->selectedprogrammestodisplay = RecruitmentHelper::getSelectedProgrammesToDisplay($id);

		$this->checklist = RecruitmentHelper::getCheckList($id);
		$this->actual_status = RecruitmentHelper::getActualStatus($this->application->status_id);
		$this->status = RecruitmentHelper::getStatus($id);

		$javascript = ($this->rights == 'write') ? "class='required validate-numeric uk-select'" : "";
		$this->lists['birth_country'] = JHtml::_('select.genericlist', RecruitmentHelper::getCountryList(), 'birth_country_id', $javascript, 'value', 'text', $this->application->birth_country_id);
		$this->lists['genders'] = JHTML::_('select.genericlist', RecruitmentHelper::getGenderList(), 'gender_id', $javascript, 'value', 'text', $this->application->gender_id);
		$this->lists['wheredidu'] = JHTML::_('select.genericlist', RecruitmentHelper::getWherediduList(), 'wheredidu_id', $javascript, 'value', 'text', $this->application->wheredidu_id);
		$this->lists['countries'] = JHtml::_('select.genericlist', RecruitmentHelper::getCountryList(), 'country_id', $javascript, 'value', 'text');

		$this->lists['doc_types'] = JHtml::_('select.genericlist', RecruitmentHelper::getDocTypeList(), 'doc_type_id', $javascript, 'value', 'text');

		$this->lists['programmes_1'] = JHtml::_('select.genericlist', RecruitmentHelper::getProgrammes(), 'programme_id_1', $javascript, 'value', 'text', $selectedprogrammes[0]);
		$this->lists['programmes_2'] = JHtml::_('select.genericlist', RecruitmentHelper::getProgrammes(), 'programme_id_2', $javascript, 'value', 'text', $selectedprogrammes[1]);
		$this->lists['programmes_3'] = JHtml::_('select.genericlist', RecruitmentHelper::getProgrammes(), 'programme_id_3', $javascript, 'value', 'text', $selectedprogrammes[2]);

		$this->lists['status'] = JHTML::_('select.genericlist', RecruitmentHelper::getStatus(), 'status_id', $javascript, 'value', 'text', $this->application->status_id);

		/*print_r($is_manager . '<hr>');
		print_r($this->job_id . '<hr>');
		print_r($this->application->firstname . '<hr>');*/

		$this->country_name = RecruitmentHelper::getCountryName($this->application->birth_country_id);
		$this->wheredidu_name = RecruitmentHelper::getWherediduName($this->application->wheredidu_id);
		$this->gender_name = RecruitmentHelper::getGenderName($this->application->gender_id);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('COM_RECRUITMENT_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $app->get('sitename');
		} elseif ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} elseif ($app->get('sitename_pagetitles', 0) == 2) {
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
