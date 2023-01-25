<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Recruitment
 * @author     Albert Moreno <albert.moreno.forrellad@gmail.com>
 * @copyright  2022 Albert Moreno
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Recruitment\Component\Recruitment\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use stdClass;
use \Recruitment\Component\Recruitment\Site\Helper\RecruitmentHelper;

use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Log\Log;



/**
 * Application class.
 *
 * @since  1.0.0
 */
class ApplicationformController extends FormController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $this->app->getUserState('com_recruitment.edit.application.id');
		$editId     = $this->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$this->app->setUserState('com_recruitment.edit.application.id', $editId);

		// Get the model.
		$model = $this->getModel('Applicationform', 'Site');

		// Check out the item
		if ($editId) {
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId) {
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&layout=edit', false));
	}

	/**
	 * Method to save data.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		$this->checkToken();

		$newentry = new stdClass();
		$newentry->id = $app_id = $this->input->get('id', null);
		$newentry->firstname = $this->input->get('firstname', null, 'string');
		$newentry->lastname = $this->input->get('lastname', null, 'string');
		$newentry->email = $this->input->get('email', null, 'string');
		$newentry->telephone = $this->input->get('telephone', null, 'string');
		$newentry->passport = $this->input->get('passport', null, 'string');
		$newentry->birth_country_id = $this->input->get('birth_country_id', null);
		$newentry->birth_date = $this->input->get('birth_date', null, 'date');
		$newentry->gender_id = $this->input->get('gender_id', null);
		$newentry->wheredidu_id = $this->input->get('wheredidu_id', null);


		if ($newentry->id) :
			// Update the object into the user profile table.
			$result = Factory::getDbo()->updateObject('#__recruitment_applications', $newentry, 'id');
			$id = $newentry->id;
		endif;

		// Redirect to the list screen.
		if (!empty($return)) {
			$this->setMessage(Text::_('COM_RECRUITMENT_ITEM_SAVED_SUCCESSFULLY'));
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 1);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $app_id));
	}

	public function save_academic($key = NULL, $urlVar = NULL)
	{
		$date = Factory::getDate();

		// Check for request forgeries.
		$this->checkToken();

		$id = $this->input->get('id', null);

		$newentry = new stdClass();
		$newentry->institution = $this->input->get('institution', null, 'string');
		$newentry->degree = $this->input->get('degree', null, 'string');
		$newentry->type = $this->input->get('type', null, 'string');
		$newentry->final_mark = $this->input->get('final_mark', null, 'string');
		$newentry->country_id = $this->input->get('country_id', null);
		$newentry->end_date = $this->input->get('end_date', null, 'date');

		$newentry->creation_date = $date->toSQL();
		$newentry->application_id = $id;

		// Insert the object into the user profile table.
		$result = Factory::getDbo()->insertObject('#__recruitment_degrees', $newentry);

		// Redirect to the list screen.
		if (!empty($return)) {
			$this->setMessage(Text::_('RECRUITMENT_INSERTION_OK'));
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 2);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	function del_academic_data()
	{
		$mainframe = &Factory::getApplication();
		$db = Factory::getDbo();
		$user = Factory::getUser();

		$academic_data_id = $this->input->get('academic_data_id', null);
		$id = $this->input->get('app_id', null);

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('applications.user_id', 'applications.job_id', 'applications.id')));
		$query->from($db->quoteName('#__recruitment_degrees', 'degrees'));
		$query->join('LEFT', $db->quoteName('#__recruitment_applications', 'applications') . ' ON (' . $db->quoteName('applications.id') . ' = ' . $db->quoteName('degrees.application_id') . ')');
		$query->where($db->quoteName('degrees.id') . ' = ' . $academic_data_id);
		$db->setQuery($query);
		$result = $db->loadObject();

		//$isManager = Helper::isManager();
		$isManager = true;

		if ($isManager || ($result->user_id == $user->id)) :
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('id') . ' = ' . $academic_data_id
			);
			$query->delete($db->quoteName('#__recruitment_degrees'));
			$query->where($conditions);
			$db->setQuery($query);
			$delete = $db->execute();
		endif;

		if ($delete) {
			$this->setMessage(Text::_('DEGREE_DELETION_OK'));
		} else {
			$this->setMessage(Text::_('DEGREE_DELETION_KO'), 'error');
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 2);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	public function save_eligibility($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		$this->checkToken();

		$newentry = new stdClass();
		$newentry->id = $app_id = $this->input->get('id', null);

		$newentry->eligibility1 = $this->input->get('eligibility1', null, 'int');
		$newentry->eligibility2 = $this->input->get('eligibility2', null, 'int');

		if ($newentry->id) :
			// Update the object into the user profile table.
			$result = Factory::getDbo()->updateObject('#__recruitment_applications', $newentry, 'id');
			$id = $newentry->id;
		endif;

		if (!empty($return)) {
			$this->setMessage(Text::_('COM_RECRUITMENT_ITEM_SAVED_SUCCESSFULLY'));
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 3);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $app_id));
	}

	public function save_docs($key = NULL, $urlVar = NULL)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$date = &Factory::getDate();

		// Check for request forgeries.
		$this->checkToken();

		$id = $this->input->get('id', null);

		//$configDocsPath = '/home/docfamicmab/data';
		$configDocsPath = 'C:\laragon\www\docfam\images\docs-docfam';

		$file = $this->input->files->get('uploaded_file');
		$file['name'] = File::makeSafe($file['name']);
		$file['name'] = preg_replace('/\s+/', '_', $file['name']);

		//Check extensions
		$uploadedFileNameParts = explode('.', $file['name']);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		$validFileExts = explode(',', 'pdf');
		$extOk = false;

		foreach ($validFileExts as $key => $value) :
			if (preg_match("/$value/i", $uploadedFileExtension)) :
				$extOk = true;
			endif;
		endforeach;

		if ($extOk == false || !$validFileExts) :
			// return an arror if the file type isn't allowed
			$session = Factory::getSession();
			$session->set('tab_id', 4);
			$this->setMessage(Text::_('INVALID-EXTENSION'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
		endif;
		//end check extensions


		$filepath = Path::clean($configDocsPath . '/' . $id . '/' . $file['name']);
		if (File::exists($filepath)) {
			$this->setMessage(Text::_('FILE_EXISTS'), 'error');
			return;
		}
		if (!File::upload($file['tmp_name'], $filepath)) {
			//handle failed upload
			return;
		}

		$newentry = new stdClass();
		$newentry->filename = $file['name'];
		$newentry->description = $this->input->get('description', null, 'string');
		$newentry->doc_type_id = $this->input->get('doc_type_id', null);

		$newentry->creation_date = $date->toSQL();
		$newentry->application_id = $id;

		// Insert the object into the user profile table.
		$result = Factory::getDbo()->insertObject('#__recruitment_docs', $newentry);

		// Redirect to the list screen.
		if (!empty($return)) {
			$this->setMessage(Text::_('RECRUITMENT_INSERTION_OK'));
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 4);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	public function del_file()
	{
		$db = Factory::getDbo();
		$user = Factory::getUser();

		// Check for request forgeries.
		$this->checkToken();

		$file_id = $this->input->get('file_id', null);
		$id = $this->input->get('app_id', null);

		$isManager = RecruitmentHelper::isManager();

		//$configDocsPath = '/home/docfamicmab/data';
		$configDocsPath = 'C:\laragon\www\docfam\images\docs-docfam';

		//Check user is able to download file
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('applications.user_id', 'applications.job_id', 'applications.id', 'docs.filename')));
		$query->from($db->quoteName('#__recruitment_docs', 'docs'));
		$query->join('LEFT', $db->quoteName('#__recruitment_applications', 'applications') . ' ON (' . $db->quoteName('applications.id') . ' = ' . $db->quoteName('docs.application_id') . ')');
		$query->where($db->quoteName('docs.id') . ' = ' . $file_id);
		$db->setQuery($query);
		$result = $db->loadObject();


		if (!($isManager || ($result->user_id == $user->id))) :
			$this->setMessage(Text::_('ALERTNOTAUTH'), 'error');
			return;
		endif;

		$filepath = Path::clean($configDocsPath . '/' . $id . '/' . $result->filename);

		if (!File::delete($filepath)) {
			$this->setMessage(Text::_('ERROR_DELETING_FILE'), 'error');
			return false;
		}

		$query = $db->getQuery(true);
		$conditions = array(
			$db->quoteName('id') . ' = ' . $file_id
		);
		$query->delete($db->quoteName('#__recruitment_docs'));
		$query->where($conditions);
		$db->setQuery($query);
		$delete = $db->execute();

		if ($delete) {
			$this->setMessage(Text::_('FILE_DELETION_OK'));
		} else {
			$this->setMessage(Text::_('FILE_DELETION_KO'), 'error');
		}

		$session = Factory::getSession();
		$session->set('tab_id', 4);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	public function download_file()
	{
		$db = Factory::getDbo();
		$user = Factory::getUser();

		$file_id = $this->input->get('file_id', null);
		$id = $this->input->get('app_id', null);
		$filename = $this->input->get('filename', null);

		//$configDocsPath = '/home/docfamicmab/data';
		$configDocsPath = 'C:\laragon\www\docfam\images\docs-docfam';

		if (!$filename) : //for referees
			//Check user is able to download file
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('applications.user_id', 'applications.job_id', 'applications.id', 'docs.filename')));
			$query->from($db->quoteName('#__recruitment_docs', 'docs'));
			$query->join('LEFT', $db->quoteName('#__recruitment_applications', 'applications') . ' ON (' . $db->quoteName('applications.id') . ' = ' . $db->quoteName('docs.application_id') . ')');
			$query->where($db->quoteName('docs.id') . ' = ' . $file_id);
			$db->setQuery($query);
			$result = $db->loadObject();

			$isManager = RecruitmentHelper::isManager();

			if (!($isManager || ($result->user_id == $user->id))) :
				$this->setMessage(Text::_('ALERTNOTAUTH'), 'error');
				return;
			endif;

			$filename = $result->filename;
		endif;

		$filepath = Path::clean($configDocsPath . '/' . $id . '/' . $filename);
		//$file_extension = strtolower(substr(strrchr($filename, "."), 1));

		//This will set the Content-Type to the appropriate setting for the file
		/*switch ($file_extension) {
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "doc":
                $ctype = "application/msword";
                break;

            //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
            case "php":
            case "htm":
            case "html":
                die("<b>Cannot be used for " . $file_extension . " files!</b>");
                break;

            default:
                $ctype = "application/force-download";
        }*/


		if (file_exists($filepath)) :
			//LOG all downloads
			// Get the date.
			/*$date = Factory::getDate()->format('Y-m-d');
			// Add the logger.
			JLog::addLogger(
				array(
					'text_file' => 'com_recruitment.' . $date . '.php',
					'text_entry_format' => '{DATETIME} {CLIENTIP} {FILENAME} {APPLICATION} {MESSAGE}'
				),
				JLog::INFO,
				'Recruitment'
			);
			$logEntry = new JLogEntry('Download File', JLog::INFO, 'Recruitment');
			$logEntry->filename = $filename;
			$logEntry->application = $id;
			JLog::add($logEntry);*/
			//END LOG


			//Begin writing headers
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");

			//Use the switch-generated Content-Type
			header("Content-Type: application/pdf");

			//Force the download
			$header = "Content-Disposition: attachment; filename=" . $filename . ";";
			header($header);
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($filepath));
			ob_end_flush();
			set_time_limit(0);
			readfile($filepath);
			exit;
		else :
			echo "File does not exists<br>";
			echo $filepath;
		endif;
	}

	public function save_referee($key = NULL, $urlVar = NULL)
	{
		$date = Factory::getDate();

		// Check for request forgeries.
		$this->checkToken();

		$id = $this->input->get('id', null);

		$newentry = new stdClass();
		$newentry->firstname = $this->input->get('firstname', null, 'string');
		$newentry->lastname = $this->input->get('lastname', null, 'string');
		$newentry->institution = $this->input->get('institution', null, 'string');
		$newentry->email = $this->input->get('email', null, 'string');
		$newentry->upload_code = mt_rand();

		$newentry->creation_date = $date->toSQL();
		$newentry->application_id = $id;

		// Insert the object into the user profile table.
		$result = Factory::getDbo()->insertObject('#__recruitment_referees', $newentry);

		// Redirect to the list screen.
		if (!empty($return)) {
			$this->setMessage(Text::_('RECRUITMENT_INSERTION_OK'));
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 5);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	function del_referee()
	{
		$mainframe = Factory::getApplication();
		$db = Factory::getDbo();
		$user = Factory::getUser();

		$referee_id = $this->input->get('referee_id', null);
		$id = $this->input->get('app_id', null);

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('applications.user_id', 'applications.job_id', 'applications.id', 'referees.filename')));
		$query->from($db->quoteName('#__recruitment_referees', 'referees'));
		$query->join('LEFT', $db->quoteName('#__recruitment_applications', 'applications') . ' ON (' . $db->quoteName('applications.id') . ' = ' . $db->quoteName('referees.application_id') . ')');
		$query->where($db->quoteName('referees.id') . ' = ' . $referee_id);
		$db->setQuery($query);
		$result = $db->loadObject();

		//$isManager = Helper::isManager();
		$isManager = true;

		if ($isManager || ($result->user_id == $user->id)) :
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('id') . ' = ' . $referee_id
			);
			$query->delete($db->quoteName('#__recruitment_referees'));
			$query->where($conditions);
			$db->setQuery($query);
			$delete = $db->execute();

			//if file, remove file too
			if ($result->filename) :
				//$configDocsPath = '/home/docfamicmab/data';
				$configDocsPath = 'C:\laragon\www\docfam\images\docs-docfam';

				$filepath = JPath::clean($configDocsPath . '/' . $result->id . '/' . $result->filename);

				if (!JFile::delete($filepath)) {
					$this->setMessage(Text::_('ERROR_DELETING_FILE'), 'error');
					parent::display();
					return false;
				}
			endif;
		endif;

		if ($delete) {
			$this->setMessage(Text::_('REFEREE_DELETION_OK'));
		} else {
			$this->setMessage(Text::_('REFEREE_DELETION_KO'), 'error');
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 5);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}


	public function save_programmes()
	{
		$date = Factory::getDate();
		$db = Factory::getDBO();

		// Check for request forgeries.
		$this->checkToken();

		$id = $this->input->get('id', null);

		$programme_id_1 = $this->input->get('programme_id_1', null);
		$programme_id_2 = $this->input->get('programme_id_2', null);
		$programme_id_3 = $this->input->get('programme_id_3', null);

		if (($programme_id_1 == $programme_id_2) || ($programme_id_1 == $programme_id_3) || ($programme_id_2 == $programme_id_3)) {
			$this->setMessage(Text::_('PROGRAMMES_SHOULD_NOT_BE_EQUAL'), 'error');
			$session = &Factory::getSession();
			$session->set('tab_id', 6);
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();
			$url  = (empty($item->link) ? 'index.php?option=com_recruitment&view=applicationform' : $item->link);
			$this->setRedirect(Route::_($url, false));
			return;
		}

		//Delete programs
		$query = "SELECT *"
			. " FROM `#__recruitment_applicationprograms` AS programs"
			. " WHERE programs.application_id=" . $id;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (count($result) > 0) {
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('application_id') . ' = ' . $id
			);
			$query->delete($db->quoteName('#__recruitment_applicationprograms'));
			$query->where($conditions);
			$db->setQuery($query);
			$delete = $db->execute();
		}

		//Add new ones
		$newentry = new stdClass();
		$newentry->creation_date = $date->toSQL();
		$newentry->application_id = $id;

		$newentry->programme_id = $programme_id_1;
		$newentry->order = 1;
		$result = Factory::getDbo()->insertObject('#__recruitment_applicationprograms', $newentry);
		$newentry->programme_id = $programme_id_2;
		$newentry->order = 2;
		$result = Factory::getDbo()->insertObject('#__recruitment_applicationprograms', $newentry);
		$newentry->programme_id = $programme_id_3;
		$newentry->order = 3;
		$result = Factory::getDbo()->insertObject('#__recruitment_applicationprograms', $newentry);

		// Redirect to the list screen.
		if (!empty($return)) {
			$this->setMessage(Text::_('RECRUITMENT_INSERTION_OK'));
		}

		$session = &Factory::getSession();
		$session->set('tab_id', 6);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	public function submit_application()
	{
		$this->checkToken();
		$date = Factory::getDate();

		$newentry = new stdClass();
		$newentry->id = $id = $this->input->get('id', null);
		$newentry->status_id = $this->input->get('status_id', null);

		$newentry->status_id = 2;
		$newentry->modification_date = $date->toSQL();
		$newentry->submit_date = $date->toSQL();
		$status = RecruitmentHelper::getActualStatus($newentry->status_id);
		$newentry->recruitment_comments = $newentry->recruitment_comments . ' <br> ' . $date->toSQL() . ' - ' . $status->description;

		$result = Factory::getDbo()->updateObject('#__recruitment_applications', $newentry, 'id');

		//send emails to all referees
		$model = Factory::getApplication()->bootComponent('com_recruitment')->getMVCFactory()->createModel('Application', 'Site');
		$application = $model->getItem($id);
		$referees = RecruitmentHelper::getReferees($id);

		foreach ($referees as $referee) :
			// get the mail subject and body from the configuration file
			$message = new stdClass();
			$message->subject = JText::_('EMAIL_REFEREE_SUBJECT');
			$message->body = JText::_('EMAIL_REFEREE_BODY');

			$message_text = str_replace("#name#", $application->firstname . " " . $application->lastname, $message->body);
			$upload_link = "https://docfam.icmab.es/index.php?option=com_recruitment&view=referee&upload_code=" . $referee->upload_code;

			$link_text = "<a href='" . $upload_link . "'>link</a>";
			$message_text = str_replace("#link#", $link_text, $message_text);

			$mailer = JFactory::getMailer();

			$mailer->setSender(array('no-reply@docfam.icmab.es', 'RECRUITMENT ICMAB'));
			$mailer->setSubject($message->subject);
			$mailer->setBody($message_text);
			$mailer->IsHTML(true);

			// Add recipients
			$mailer->addRecipient($referee->email);

			if ($mailer->Send()) :
				$this->setMessage(Text::_('MAIL_REF_CORRECTLY_SEND'));
				$updatereferee = new stdClass();
				$updatereferee->id = $referee->id;
				$updatereferee->sent_email = $date->toSQL();
				$result = JFactory::getDbo()->updateObject('#__recruitment_referees', $updatereferee, 'id');
				$send_email_ok = true;
			else :
				$send_email_ok = false;
			endif;

		endforeach;

		/* Mail al usuari */
		$new_status = RecruitmentHelper::getActualStatus($newentry->status_id);
		//$application = $model->getItem($id);

		$message_text = str_replace("#name#", $application->firstname . " " . $application->lastname, $new_status->email_body);
		$message_text = str_replace("#status#", $new_status->description, $message_text);

		$mailer = Factory::getMailer();

		$mailer->setSender(array('no-reply@docfam.icmab.es', 'RECRUITMENT ICMAB'));
		$mailer->setSubject($new_status->email_subject);
		$mailer->setBody($message_text);
		$mailer->IsHTML(true);

		// Add recipients
		$mailer->addRecipient($application->email);

		if ($mailer->Send()) :
			$this->setMessage(Text::_('MAIL_CORRECTLY_SEND'));
		endif;

		/* END EMAIL USER */

		$this->setMessage(Text::_('RECRUITMENT_INSERTION_OK'));

		$session = &Factory::getSession();
		$session->set('tab_id', 7);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}

	public function change_status()
	{
		// Check for request forgeries.
		$this->checkToken();
		$date = Factory::getDate();

		$newentry = new stdClass();
		$newentry->id = $this->input->get('id', null);
		$newentry->status_id = $this->input->get('status_id', null);
		$newentry->job_id = $this->input->get('job_id', null);

		$application = RecruitmentHelper::getApplication($this->input->get('job_id', null));

		//Afegir new status line si canvi:
		if ($newentry->status_id != $application->status_id) :
			$status = RecruitmentHelper::getActualStatus($newentry->status_id);
			$newentry->recruitment_comments = $this->input->get('recruitment_comments', null, 'raw') . ' <br> ' . $date->toSQL() . ' - ' . $status->description;
		//$newentry->recruitment_comments = str_replace('br','<br>',$newentry->recruitment_comments);
		else :
			$newentry->recruitment_comments = $this->input->get('recruitment_comments', null, 'raw');
		endif;


		$newentry->modification_date = $date->toSQL();

		$result = Factory::getDbo()->updateObject('#__recruitment_applications', $newentry, 'id');

		$send_email = $this->input->get('send_email', null);

		if ($send_email == 'yes') :
			$new_status = RecruitmentHelper::getActualStatus($newentry->status_id);
			//$application = $model->getItem($id);

			$message_text = str_replace("#name#", $application->firstname . " " . $application->lastname, $new_status->email_body);
			$message_text = str_replace("#status#", $new_status->description, $message_text);

			$mailer = Factory::getMailer();

			$mailer->setSender(array('no-reply@docfam.icmab.es', 'RECRUITMENT ICMAB'));
			$mailer->setSubject($new_status->email_subject);
			$mailer->setBody($message_text);
			$mailer->IsHTML(true);

			// Add recipients
			$mailer->addRecipient($application->email);

			if ($mailer->Send()) :
				$this->setMessage(Text::_('MAIL_CORRECTLY_SEND'));
				$updatereferee = new stdClass();
				$send_email_ok = true;
			else :
				$send_email_ok = false;
			endif;

		//LOG all emails sent to referees
		/*if ($send_email_ok) :
				// Get the date.
				$date = JFactory::getDate()->format('Y-m-d');
				// Add the logger.
				JLog::addLogger(
					array(
						'text_file' => 'com_recruitment.' . $date . '.php',
						'text_entry_format' => '{DATETIME} {CLIENTIP} {EMAIL} {APPLICATION} {NEWSTATUS} {MESSAGE}'
					),
					JLog::INFO,
					'Recruitment'
				);
				$logEntry = new JLogEntry('Applicant Email', JLog::INFO, 'Recruitment');
				$logEntry->email = $application->email;
				$logEntry->application = $id;
				$logEntry->newstatus = $newentry->status_id;
				JLog::add($logEntry);
			endif;*/
		//END LOG
		endif;

		$this->setMessage(Text::_('RECRUITMENT_INSERTION_OK'));

		$session = &Factory::getSession();
		$session->set('tab_id', 7);

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=applicationform&id=' . $id));
	}


	function save_referee_file()
	{
		$date = Factory::getDate();

		// Check for request forgeries.
		$this->checkToken();

		$upload_code = $this->input->get('upload_code', null);
		$application_id = $this->input->get('application_id', null);
		$referee_id = $this->input->get('id', null);

		//$configDocsPath = '/home/docfamicmab/data';
		$configDocsPath = 'C:\laragon\www\docfam\images\docs-docfam';

		$file = $this->input->files->get('uploaded_file');
		$file['name'] = File::makeSafe($file['name']);
		$file['name'] = preg_replace('/\s+/', '_', $file['name']);

		//Check extensions
		$uploadedFileNameParts = explode('.', $file['name']);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		$validFileExts = explode(',', 'pdf');
		$extOk = false;

		foreach ($validFileExts as $key => $value) :
			if (preg_match("/$value/i", $uploadedFileExtension)) :
				$extOk = true;
			endif;
		endforeach;

		if ($extOk == false || !$validFileExts) :
			// return an arror if the file type isn't allowed
			$this->setMessage(Text::_('INVALID-EXTENSION'), 'error');
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();
			$url  = (empty($item->link) ? 'index.php?option=com_recruitment&view=referee&layout=default&upload_code=' . $upload_code : $item->link);
			$this->setRedirect(Route::_($url, false));
		endif;
		//end check extensions

		$filepath = Path::clean($configDocsPath . '/' . $application_id . '/' . $file['name']);

		if (File::exists($filepath)) {
			$this->setMessage(Text::_('FILE_EXISTS'), 'error');
			return;
		}
		if (!File::upload($file['tmp_name'], $filepath)) {
			//handle failed upload
			return;
		}

		$newentry = new stdClass();
		$newentry->filename = $file['name'];
		$newentry->upload_date = $date->toSQL();
		$newentry->id = $referee_id;

		$result = Factory::getDbo()->updateObject('#__recruitment_referees', $newentry, 'id');

		$this->setMessage(Text::_('REFEREE_FILE_INSERTION_OK'));

		//Send Message to user

		$referee_name = RecruitmentHelper::getRefereeName($referee_id);
		$model = $this->getModel();
		$application = $model->getItem($application_id);
		$email_subject = "DOC-FAM COFUND Doctoral Programme: Referee file upload";
		$email_body = "
Dear #name#,
<br><br>
this email is to inform you that #nombrereferee# has uploaded a recommendation letter for your application to DOC-FAM H2020-MSCA-COFUND doctoral programme
<br><br>
Regards,<br>
DOC-FAM Management Team<br>
<i>(This is an automatically generated message. Please do not reply)</i>
        ";

		$message_text = str_replace("#name#", $application->firstname . " " . $application->lastname, $email_body);
		$message_text = str_replace("#nombrereferee#", $referee_name->firstname . " " . $referee_name->lastname, $message_text);

		$mailer = JFactory::getMailer();
		$mailer->setSender(array('no-reply@icmab.es', 'RECRUITMENT ICMAB'));
		$mailer->setSubject($email_subject);
		$mailer->setBody($message_text);
		$mailer->IsHTML(true);
		$mailer->addRecipient($application->email);
		if ($mailer->Send()) :
			$this->setMessage(Text::_('MAIL_CORRECTLY_SEND'));
		endif;
		//End Sending Message

		$this->setRedirect(Route::_('index.php?option=com_recruitment&view=referee&layout=default&upload_code=' . $upload_code));
	}

	/**
	 * Method to abort current operation
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function cancel($key = NULL)
	{

		// Get the current edit id.
		$editId = (int) $this->app->getUserState('com_recruitment.edit.application.id');

		// Get the model.
		$model = $this->getModel('Applicationform', 'Site');

		// Check in the item
		if ($editId) {
			$model->checkin($editId);
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_recruitment&view=applications' : $item->link);
		$this->setRedirect(Route::_($url, false));
	}

	/**
	 * Method to remove data
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function remove()
	{
		$model = $this->getModel('Applicationform', 'Site');
		$pk    = $this->input->getInt('id');

		// Attempt to save the data
		try {
			// Check in before delete
			$return = $model->checkin($return);
			// Clear id from the session.
			$this->app->setUserState('com_recruitment.edit.application.id', null);

			$menu = $this->app->getMenu();
			$item = $menu->getActive();
			$url = (empty($item->link) ? 'index.php?option=com_recruitment&view=applications' : $item->link);

			if ($return) {
				$model->delete($pk);
				$this->setMessage(Text::_('COM_RECRUITMENT_ITEM_DELETED_SUCCESSFULLY'));
			} else {
				$this->setMessage(Text::_('COM_RECRUITMENT_ITEM_DELETED_UNSUCCESSFULLY'), 'warning');
			}


			$this->setRedirect(Route::_($url, false));
			// Flush the data from the session.
			$this->app->setUserState('com_recruitment.edit.application.data', null);
		} catch (\Exception $e) {
			$errorType = ($e->getCode() == '404') ? 'error' : 'warning';
			$this->setMessage($e->getMessage(), $errorType);
			$this->setRedirect('index.php?option=com_recruitment&view=applications');
		}
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   BaseDatabaseModel  $model      The data model object.
	 * @param   array              $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
	{
	}
}
