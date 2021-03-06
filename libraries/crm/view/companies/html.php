<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class CobaltViewCompaniesHtml extends JViewHTML
{
    public function render()
    {
        $app = JFactory::getApplication();
        $app->input->set('view','companies');
        $app->input->set('layout',$app->input->get('layout','default'));
        
        //get model
        $model = new CobaltModelCompany();
        $state = $model->getState();
        
        //session data
        $session = JFactory::getSession();
        $member_role = CobaltHelperUsers::getRole();
        $user_id = CobaltHelperUsers::getUserId();
        $team_id = CobaltHelperUsers::getTeamId();
        $company = $session->get('company_type_filter');
        $user = $session->get('company_user_filter');
        $team = $session->get('company_team_filter');
        
        //load java libs
        $doc = JFactory::getDocument();
        $doc->addScript( JURI::base().'libraries/crm/media/js/company_manager.js' );
        
        //determine if we are requesting a specific company or all companies
        //if id requested
        if( $app->input->get('id') ) {
            $companies = $model->getCompanies($app->input->get('id'));  
            if ( is_null($companies[0]['id']) ){
                $app = JFactory::getApplication();
                $app->redirect(JRoute::_('index.php?view=companies'),CRMText::_('COBALT_NOT_AUTHORIZED'));
            }
        }else{
            //else load all companies
            if ( $app->input->get('layout') != 'edit' ){ 
                $companies = $model->getCompanies();
            }
        }
        
        //assign pagination
        $pagination = $model->getPagination();
        $this->pagination = $pagination;
        
        
        //get company type filters
        $company_types = CobaltHelperCompany::getTypes();
        $company_type = ( $company ) ? $company_types[$company] : $company_types['all'];
        
        //get user filter
        if ( $user AND $user != $user_id AND $user != 'all' ){
            $user_info = CobaltHelperUsers::getUsers($user);
            $user_info = $user_info[0];
            $user_name = $user_info['first_name'] . " " . $user_info['last_name'];
        }else if ( $team ){
            $team_info = CobaltHelperUsers::getTeams($team);
            $team_info = $team_info[0];
            $user_name = $team_info['team_name'].CRMText::_('COBALT_TEAM_APPEND');
        }else if ( $user == 'all' || $user == "" ) {
            $user_name = CRMText::_('COBALT_ALL_USERS');
        }else{
            $user_name = CRMText::_('COBALT_ME');            
        }

        //get associated members and teams
        $teams = CobaltHelperUsers::getTeams();
        $users = CobaltHelperUsers::getUsers();
        
        //get total associated companies for count display
        $company_count = CobaltHelperUsers::getCompanyCount($user_id,$team_id,$member_role);

        //Load Events & Tasks for person
        $layout = $app->input->get('layout');

        switch ($layout) {
            case 'company':

                $model = new CobaltModelEvent();
                $events = $model->getEvents("company",null,$app->input->get('id'));

                $this->event_dock = CobaltHelperView::getView('events','event_dock', 'phtml',array('events'=>$events));
                $this->deal_dock = CobaltHelperView::getView('deals','deal_dock','phtml',array('deals'=>$companies[0]['deals']));
                $this->document_list = CobaltHelperView::getView('documents','document_row','phtml',array('documents'=>$companies[0]['documents']));
                $this->people_dock = CobaltHelperView::getView('people','people_dock','html',array('people'=>$companies[0]['people']));

                $custom_fields_view = CobaltHelperView::getView('custom','default','html');
                $type = "company";
                $custom_fields_view->type = $type;
                $custom_fields_view->item = $companies[0];
                $this->custom_fields_view = $custom_fields_view;

                if ( CobaltHelperBanter::hasBanter() ){
                    $room_list = new CobaltHelperTranscriptlists();
                    $room_lists = $room_list->getRooms();
                    $transcripts = array();
                    if ( is_array($room_lists) && count($room_lists) > 0 ) { 
                        $transcripts = $room_list->getTranscripts($room_lists[0]->id);
                    }
                    $banter_dock = CobaltHelperView::getView('banter','default','html');
                    $banter_dock->rooms = $room_lists;
                    $banter_dock->transcripts = $transcripts;
                    $this->banter_dock = $banter_dock;
                }

                if ( CobaltHelperTemplate::isMobile() ){
                    $add_note = CobaltHelperView::getView('note','edit','html');
                    $this->add_note = $add_note;
                }

            break;
            case 'default':
            default:

                //get column filters
                $this->column_filters = CobaltHelperCompany::getColumnFilters();
                $this->selected_columns = CobaltHelperCompany::getSelectedColumnFilters();

                $company_list = CobaltHelperView::getView('companies','list','html',array('companies'=>$companies));
                $total = $model->getTotal();
                $pagination = $model->getPagination();
                $company_list->total = $total;
                $company_list->pagination = $pagination;
                $this->company_list = $company_list;
                $company_name = $state->get('Company.companies_name');
                $this->company_filter = $company_name;
            break;

            case 'edit':
                $item = $app->input->get('id') && array_key_exists(0,$companies) ? $companies[0] : array('id'=>'');
                $edit_custom_fields_view = CobaltHelperView::getView('custom','edit','html');
                $type = "company";
                $edit_custom_fields_view->type = $type;
                $edit_custom_fields_view->item = $item;
                $this->edit_custom_fields_view = $edit_custom_fields_view;
            break;

        }
        
        //ref assignments
        $this->companies=$companies;
        $this->user_id=$user_id;
        $this->member_role=$member_role;
        $this->teams=$teams;
        $this->users=$users;
        $this->company_types=$company_types;
        $this->company_type=$company_type;
        $this->user_name=$user_name;
        $this->company_count=$company_count;
        $this->state=$state;

        //display
        return parent::render();
    }
    
}