<?php
/**
 * RoostEmployeeList Listing
 * @author  Franklys Guimaraes
 */
class RoostEmployeeList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Employee');
        $this->form->setFormTitle('<strong> BUSQUE SEUS FUNCIONÁRIOS</strong>');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'name');
        $name = new TEntry('name');
        $name->forceUpperCase();
        $name->setMask('S!');
        $document = new TEntry('document');
        $document->setMask('999.999.999-99');
        $contact = new TEntry('contact');
        $contact->setMask('(99) 99999-9999');
        $salary = new TEntry('salary');
        $salary->setNumericMask(2, '.', ',', true);
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');


        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( [ new TLabel('Nome'), $name ],
                                [ new TLabel('Documento'), $document ],
                                [ new TLabel('Reginstrado em'), $created_at ],
                                [ new TLabel('Até'), $updated_at ] );

        $row->layout = ['col-sm-4','col-sm-4','col-sm-2','col-sm-2'];
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Cadastrar novo', new TAction(['RoostEmployeeForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'left');
        $column_name = new TDataGridColumn('name', 'NOME', 'left');
        $column_document = new TDataGridColumn('document', 'DOCUMENTO', 'left');
        $column_contact = new TDataGridColumn('contact', 'CONTATO', 'left');
        $column_salary = new TDataGridColumn('salary', 'SALARIO', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'Created At', 'left');
        $column_updated_at = new TDataGridColumn('updated_at', 'ULTIMA ATUALIZAÇÃO', 'right');

        $column_salary->setTransformer(function($value){
            return Convert::toMonetario($value);
        });
        
        $column_document->setTransformer(function($value){
            return Convert::toCPF_CNPJ($value);
        });

        $column_updated_at->setTransformer(function($value){
            return Convert::toDate($value, 'd / m / Y');
        });

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_document);
        $this->datagrid->addColumn($column_contact);
        $this->datagrid->addColumn($column_salary);
        // $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);


        $action1 = new TDataGridAction(['RoostEmployeeForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('app'); // open a transaction with database
            $object = new Employee($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue(__CLASS__.'_filter_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_system_user_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_name',   NULL);
        TSession::setValue(__CLASS__.'_filter_document',   NULL);
        TSession::setValue(__CLASS__.'_filter_contact',   NULL);
        TSession::setValue(__CLASS__.'_filter_salary',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);
        TSession::setValue(__CLASS__.'_filter_updated_at',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->system_user_id) AND ($data->system_user_id)) {
            $filter = new TFilter('system_user_id', '=', $data->system_user_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_system_user_id',   $filter); // stores the filter in the session
        }


        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_name',   $filter); // stores the filter in the session
        }


        if (isset($data->document) AND ($data->document)) {
            $filter = new TFilter('document', 'like', "%{$data->document}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_document',   $filter); // stores the filter in the session
        }


        if (isset($data->contact) AND ($data->contact)) {
            $filter = new TFilter('contact', 'like', "%{$data->contact}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_contact',   $filter); // stores the filter in the session
        }


        if (isset($data->salary) AND ($data->salary)) {
            $filter = new TFilter('salary', 'like', "%{$data->salary}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_salary',   $filter); // stores the filter in the session
        }


        if ((isset($data->created_at) AND ($data->created_at)) AND (isset($data->updated_at) AND ($data->updated_at))) {
            $filter = new TFilter('created_at', 'between', "{$data->created_at}", "{$data->updated_at}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_created_at',   $filter); // stores the filter in the session
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'app'
            TTransaction::open('app');
            
            // creates a repository for Employee
            $repository = new TRepository('Employee');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_system_user_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_system_user_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_name')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_name')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_document')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_document')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_contact')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_contact')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_salary')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_salary')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_created_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_created_at')); // add the session filter
            }

            // citerios especificos
            $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('userid'))); 
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('app'); // open a transaction with database
            $object = new Employee($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
