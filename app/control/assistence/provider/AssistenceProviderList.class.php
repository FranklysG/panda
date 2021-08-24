<?php
/**
 * AssistenceProviderList Listing
 * @author  Franklys Guimaraes
 */
class AssistenceProviderList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Provider');
        $this->form->setFormTitle('<strong> BUSQUE SEUS FORNECEDORES</strong>');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $name = new TEntry('name');
        $cnpj = new TEntry('cnpj');
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');


        // add the fields
        $this->form->addFields( [ new TLabel('Nome'), $name ],
                                [ new TLabel('Cnpj'), $cnpj ],
                                [ new TLabel('Criado em'), $created_at ],
                                [ new TLabel('Até'), $updated_at ] );

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Cadastrar novo', new TAction(['AssistenceProviderForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'right');
        $column_name = new TDataGridColumn('name', 'NOME', 'left');
        $column_cnpj = new TDataGridColumn('cnpj', 'CNPJ', 'left');
        $column_contact = new TDataGridColumn('contact', 'CONTATO', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'Created At', 'left');
        $column_updated_at = new TDataGridColumn('updated_at', 'ULTIMA ATUALIZAÇÃO', 'right');

        $column_updated_at->setTransformer(function($value){
            return Convert::toDate($value, 'd / m / Y');
        });

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_cnpj);
        $this->datagrid->addColumn($column_contact);
        // $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);


        $action1 = new TDataGridAction(['AssistenceProviderForm', 'onEdit'], ['id'=>'{id}']);
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
            $object = new Provider($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_name',   NULL);
        TSession::setValue(__CLASS__.'_filter_cnpj',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);
        TSession::setValue(__CLASS__.'_filter_updated_at',   NULL);

        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_name',   $filter); // stores the filter in the session
        }


        if (isset($data->cnpj) AND ($data->cnpj)) {
            $filter = new TFilter('cnpj', 'like', "%{$data->cnpj}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_cnpj',   $filter); // stores the filter in the session
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
            
            // creates a repository for Provider
            $repository = new TRepository('Provider');
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
            

            if (TSession::getValue(__CLASS__.'_filter_name')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_name')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_cnpj')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cnpj')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_created_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_created_at')); // add the session filter
            }
            
            // citerios especificos
            $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
            foreach ($system_user_unit as $value) {
                $ids[] = $value->system_user_id;
            }
            $criteria->add(new TFilter('system_user_id','IN', $ids));
            
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
            $object = new Provider($key, FALSE); // instantiates the Active Record
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
