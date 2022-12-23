<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Recruitment
 * @author     Albert Moreno <albert.moreno.forrellad@gmail.com>
 * @copyright  2022 Albert Moreno
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Recruitment\Component\Recruitment\Site\Helper;

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Recruitment\Component\Recruitment\Site\Model\ApplicationModel;

/**
 * Class RecruitmentFrontendHelper
 *
 * @since  1.0.0
 */
class RecruitmentHelper
{


    /**
     * Gets the files attached to an item
     *
     * @param   int     $pk     The item's id
     *
     * @param   string  $table  The table's name
     *
     * @param   string  $field  The field's name
     *
     * @return  array  The files
     */
    public static function getFiles($pk, $table, $field)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);

        $query
            ->select($field)
            ->from($table)
            ->where('id = ' . (int) $pk);

        $db->setQuery($query);

        return explode(',', $db->loadResult());
    }

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = Factory::getApplication()->getIdentity();

        if ($user->authorise('core.edit', 'com_recruitment') || (isset($item->created_by) && $user->authorise('core.edit.own', 'com_recruitment') && $item->created_by == $user->id) || $user->authorise('core.create', 'com_recruitment')) {
            $permission = true;
        }

        return $permission;
    }

    public static function getApplication($job_id = null)
    {
        $user = Factory::getUser();
        $db = &Factory::getDBO();
        $date = &Factory::getDate();

        $query = "SELECT application.id"
            . " FROM `#__recruitment_applications` AS application"
            . " WHERE application.user_id=" . $user->id
            . " AND application.job_id=" . $job_id;

        $db->setQuery($query);
        $app_id = $db->loadResult();

        $model = Factory::getApplication()->bootComponent('com_recruitment')->getMVCFactory()->createModel('Application', 'Site');

        //$model = JModelLegacy::getInstance('application');
        //$model = JModel::getInstance('application', 'RecruitmentModel');
        //$this->getModel()->getItem($recordId);

        if ($app_id) :
            return $model->getItem($app_id);
        else :
            //crear i retornar
            $newentry = new \stdClass;
            $newentry->creation_date = $date->toSQL();
            $newentry->user_id = $user->id;
            $newentry->job_id = $job_id;
            $newentry->status_id = 1;
            // Insert the object into the user profile table.
            $result = Factory::getDbo()->insertObject('#__recruitment_applications', $newentry);
            $id = $db->insertid();
            return $model->getItem($id);
        endif;
    }


    public static function getJob($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT jobs.*"
            . " FROM `#__recruitment_jobs` AS jobs"
            . " WHERE jobs.id=" . $id;

        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function isManager($id = null)
    {
        $user = Factory::getUser();
        return in_array(8, $user->get('groups'));
    }

    public static function isEvaluator($id = null)
    {
        $user = JFactory::getUser();
        return in_array(10, $user->get('groups'));
        /*$db =& JFactory::getDBO();

        $query = "SELECT count(evaluator.user_id)"
            . " FROM `#__recruitment_application_evaluator` AS evaluator"
            . " WHERE evaluator.application_id=" . $id
            . " AND evaluator.user_id=" . $user->id
        ;

        $db->setQuery($query);
        return $db->loadResult();*/
    }

    function getActualStatus($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT status.*"
            . " FROM `#__recruitment_status` AS status"
            . " WHERE status.id=" . $id;

        $db->setQuery($query);
        return $db->loadObject();
    }

    function getChecklist($id = null)
    {

        $db = &JFactory::getDBO();
        $checklist = new stdClass();

        $checklist->allow_submit = true;

        //Check Closing date
        $query = "SELECT jobs.closing_date"
            . " FROM `#__recruitment_applications` AS app"
            . " LEFT JOIN `#__recruitment_jobs` AS jobs"
            . " on jobs.id = app.job_id"
            . " WHERE app.id=" . $id;
        $db->setQuery($query);
        $closing_date = new JDate($db->loadResult());
        $currentTime = new JDate('now');
        if ($currentTime > $closing_date) :
            $checklist->too_late = true;
            $checklist->allow_submit = false;
        else :
            $checklist->too_late = false;
        endif;

        //Check Personal data exists
        $query = "SELECT *"
            . " FROM `#__recruitment_applications` AS app"
            . " WHERE app.id=" . $id;
        $db->setQuery($query);
        $personal_data = $db->loadObject();
        if (
            $personal_data->firstname &&
            $personal_data->lastname &&
            (!is_null($personal_data->birth_country_id)) &&
            (!is_null($personal_data->gender_id)) &&
            (!is_null($personal_data->wheredidu_id))
        ) :
            $checklist->personal_data = true;
        else :
            $checklist->personal_data = false;
            $checklist->allow_submit = false;
        endif;

        $published_tabs = Helper::getPublishedTabs($personal_data->job_id);

        if (in_array('2', $published_tabs)) :
            //Check Degrees
            $degrees = Helper::getDegrees($id);
            if (count($degrees) > 0) :
                $checklist->degrees = true;
            else :
                $checklist->degrees = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check Work Experience
        if (in_array('3', $published_tabs)) :
            $work_experience = Helper::getWorkExperiences($id);
            if (count($work_experience) > 0) :
                $checklist->work_experience = true;
            else :
                $checklist->work_experience = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check NO CV
        if (in_array('4', $published_tabs)) :
            $query = "SELECT docs.id"
                . " FROM `#__recruitment_docs` AS docs"
                . " WHERE docs.application_id=" . $id
                . " AND docs.doc_type_id= '1'";
            $db->setQuery($query);
            if ($db->loadResult()) :
                $checklist->cv = true;
            else :
                $checklist->cv = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check No motivation letter
        if (in_array('4', $published_tabs)) :
            $query = "SELECT docs.id"
                . " FROM `#__recruitment_docs` AS docs"
                . " WHERE docs.application_id=" . $id
                . " AND docs.doc_type_id= '2'";
            $db->setQuery($query);
            if ($db->loadResult()) :
                $checklist->motivation_letter = true;
            else :
                $checklist->motivation_letter = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check No academic records
        if (in_array('4', $published_tabs)) :
            $query = "SELECT docs.id"
                . " FROM `#__recruitment_docs` AS docs"
                . " WHERE docs.application_id=" . $id
                . " AND docs.doc_type_id= '3'";
            $db->setQuery($query);
            if ($db->loadResult()) :
                $checklist->academic_records = true;
            else :
                $checklist->academic_records = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check No Proof of English Level
        /*if (in_array('4', $published_tabs)):
            $query = "SELECT docs.id"
                . " FROM `#__recruitment_docs` AS docs"
                . " WHERE docs.application_id=" . $id
                . " AND docs.doc_type_id= '5'";
            $db->setQuery($query);
            if ($db->loadResult()):
                $checklist->english_level = true;
            else:
                $checklist->english_level = false;
                $checklist->allow_submit = false;
            endif;
        endif;*/

        //Check Referees
        if (in_array('5', $published_tabs)) :
            $referees = Helper::getReferees($id);
            if (count($referees) >= 1) :
                $checklist->referees = true;
            else :
                $checklist->referees = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check Programmes
        if (in_array('6', $published_tabs)) :
            $selectedprogrammes = Helper::getSelectedProgrammes($id);
            if ($selectedprogrammes) :
                $checklist->selectedprogrammes = true;
            else :
                $checklist->selectedprogrammes = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        //Check Eligibility
        if (in_array('8', $published_tabs)) :
            if (($personal_data->eligibility1 == '1') && ($personal_data->eligibility2 == '1')) :
                $checklist->eligibility = true;
            else :
                $checklist->eligibility = false;
                $checklist->allow_submit = false;
            endif;
        endif;

        return $checklist;
    }

    function getMyApplications()
    {
        $user = JFactory::getUser();
        $db = &JFactory::getDBO();

        $query = "SELECT application.*, jobs.short_description, jobs.description, jobs.closing_date, status.description as status"
            . " FROM `#__recruitment_applications` AS application"
            . " LEFT JOIN `#__recruitment_jobs` AS jobs"
            . " on jobs.id = application.job_id"
            . " LEFT JOIN `#__recruitment_status` AS status"
            . " on status.id = application.status_id"
            . " WHERE application.user_id=" . $user->id
            . " ORDER BY jobs.closing_date DESC";

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function canMigrateData($application)
    {
        if (($application->status_id == '1') &&
            ($application->firstname == '') &&
            ($application->lastname == '') &&
            ($application->email == '') &&
            ($application->modification_date == '') &&
            ($application->submit_date == '')
        ) :
            return true;
        endif;

        return false;
    }

    function findFromUploadCode($upload_code)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT referee.*"
            . " FROM `#__recruitment_referees` AS referee"
            . " WHERE referee.upload_code=" . $upload_code;

        $db->setQuery($query);
        return $db->loadObject();
    }

    /*
    * GET APPLICATION ADDITIONAL DATA
    * */

    function getDegrees($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT degrees.*"
            . " FROM `#__recruitment_degrees` AS degrees"
            . " WHERE degrees.application_id=" . $id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getWorkExperiences($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT work.*, countries.printable_name"
            . " FROM `#__recruitment_workexperiences` AS work"
            . " LEFT JOIN `#__recruitment_countries` AS countries"
            . " on countries.id = work.country_id"
            . " WHERE work.application_id=" . $id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /*function getFiles($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT docs.*, doc_types.description"
            . " FROM `#__recruitment_docs` AS docs"
            . " LEFT JOIN `#__recruitment_doctypes` AS doc_types"
            . " on doc_types.id = docs.doc_type_id"
            . " WHERE docs.application_id=" . $id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }*/

    function getReferees($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT referees.*"
            . " FROM `#__recruitment_referees` AS referees"
            . " WHERE referees.application_id=" . $id;

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getRefereeName($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT referees.*"
            . " FROM `#__recruitment_referees` AS referees"
            . " WHERE referees.id=" . $id;

        $db->setQuery($query);
        return $db->loadObject();
    }

    function getSelectedProgrammes($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT ap.programme_id"
            . " FROM `#__recruitment_applicationprograms` AS ap"
            . " WHERE ap.application_id=" . $id
            . " ORDER BY ap.order";

        $db->setQuery($query);
        return $db->loadColumn();
    }

    function getSelectedProgrammesToDisplay($id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT programmes.description"
            . " FROM `#__recruitment_applicationprograms` AS ap"
            . " LEFT JOIN `#__recruitment_programmes` AS programmes"
            . " on programmes.id = ap.programme_id"
            . " WHERE ap.application_id=" . $id
            . " ORDER BY ap.order";

        $db->setQuery($query);
        return $db->loadColumn();
    }

    /*
     * SELECTORS
     * */

    function getGenderList()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.description AS text"
            . " FROM `#__recruitment_genders` AS g"
            . " ORDER BY g.order";
        $db->setQuery($query);
        $genderslist[] = JHTML::_('select.option', '', JText::_('- Select Gender -'), 'value', 'text');
        $genderslist = array_merge($genderslist, $db->loadObjectList());
        return $genderslist;
    }

    function getCountryList()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.printable_name AS text"
            . " FROM `#__recruitment_countries` AS g"
            . " ORDER BY g.printable_name";
        $db->setQuery($query);
        $countrieslist[] = JHTML::_('select.option', '', JText::_('- Select Country -'), 'value', 'text');
        $countrieslist = array_merge($countrieslist, $db->loadObjectList());
        return $countrieslist;
    }

    function getWherediduList()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.description AS text"
            . " FROM `#__recruitment_wheredidus` AS g"
            . " ORDER BY g.order";
        $db->setQuery($query);
        $wheredidulist[] = JHTML::_('select.option', '', JText::_('- Select Option -'), 'value', 'text');
        $wheredidulist = array_merge($wheredidulist, $db->loadObjectList());
        return $wheredidulist;
    }

    function getOverallRange()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.description AS text"
            . " FROM `#__recruitment_overallranges` AS g"
            . " ORDER BY g.order";
        $db->setQuery($query);
        $overallrangeslist[] = JHTML::_('select.option', '', JText::_('- Select Option -'), 'value', 'text');
        $overallrangeslist = array_merge($overallrangeslist, $db->loadObjectList());
        return $overallrangeslist;
    }

    function getDocTypeList()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.description AS text"
            . " FROM `#__recruitment_doctypes` AS g"
            . " ORDER BY g.order";
        $db->setQuery($query);
        $doctypelist[] = JHTML::_('select.option', '', JText::_('- Select Doc Type -'), 'value', 'text');
        $doctypelist = array_merge($doctypelist, $db->loadObjectList());
        return $doctypelist;
    }

    function getProgrammes()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.description AS text"
            . " FROM `#__recruitment_programmes` AS g"
            . " ORDER BY g.order";
        $db->setQuery($query);
        $programlist[] = JHTML::_('select.option', '', JText::_('- Select Programme -'), 'value', 'text');
        $programlist = array_merge($programlist, $db->loadObjectList());
        return $programlist;
    }

    function getStatus()
    {
        $db = &JFactory::getDBO();

        $query = "SELECT g.id AS value, g.description AS text"
            . " FROM `#__recruitment_status` AS g"
            . " ORDER BY g.order";
        $db->setQuery($query);
        $statuslist[] = JHTML::_('select.option', '', JText::_('- Select Status -'), 'value', 'text');
        $statuslist = array_merge($statuslist, $db->loadObjectList());
        return $statuslist;
    }

    function getPublishedTabs($job_id = null)
    {
        $db = &JFactory::getDBO();

        $query = "SELECT tab_id"
            . " FROM `#__recruitment_tabsjobs` AS tj"
            . " WHERE tj.job_id=" . $job_id;

        $db->setQuery($query);
        return $db->loadColumn();
    }
}
