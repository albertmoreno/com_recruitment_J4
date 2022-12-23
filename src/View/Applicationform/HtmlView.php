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

		$is_manager = RecruitmentHelper::isManager();

		//receive job id and $user
		$jinput = Factory::getApplication()->input;
		$this->job_id = $jinput->get('job_id', '3');
		$this->app_id = $id = $jinput->get('id');

		$session = &Factory::getSession();
		$this->tab_id = $session->get('tab_id');

		if ($id) :
			$this->application = $this->get('Item');
			$this->job_id = $this->application->job_id;
		else :
			$this->application = RecruitmentHelper::getApplication($this->job_id);
			$id = $this->application->id;
		endif;

		print_r($is_manager . '<hr>');
		print_r($this->job_id . '<hr>');
		print_r($this->application->firstname . '<hr>');


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
