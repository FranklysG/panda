<?php

/**
 * RestaurantProductList Listing
 * @author  Franklys Guimaraes
 */
class RestaurantProductList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Product');
        $this->form->setFormTitle('<strong> BUSQUE SEUS PRODUTOS</strong>');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'name');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('userid')));
        $product_id = new TDBUniqueSearch('product_id', 'app', 'Product', 'id', 'name', null, $criteria);
        $product_id->setMinLength(1);
        $product_id->setMask('(SKU: {sku}) {name} ');
        $sku = new TEntry('sku');
        $name = new TEntry('name');
        $alias = new TEntry('alias');
        $status = new TCombo('status');
        $status->addItems(
            [
                '1' => 'ATIVO',
                '0' => 'INATIVO'
            ]
        );
        $status->setDefaultOption(false);
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');


        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( [ new TLabel('Sku'), $sku ],
                                [ new TLabel('Nome'), $product_id ],
                                [ new TLabel('Status'), $status ],
                                [ new TLabel('Criado em'), $created_at ],
                                [ new TLabel('até'), $updated_at ]);

        $row->layout = ['col-sm-2','col-sm-4','col-sm-2','col-sm-2','col-sm-2',];
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Cadastrar novo', new TAction(['RestaurantProductForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Produto', "<img style='max-height: 300px' src='tmp/".TSession::getvalue('userid')."/{image}'>");

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_system_user_id = new TDataGridColumn('system_user_id', 'System User Id', 'right');
        $column_sku = new TDataGridColumn('sku', 'SKU', 'left');
        $column_brand = new TDataGridColumn('brand->name', 'MARCA', 'left');
        $column_name = new TDataGridColumn('name', 'NOME', 'left');
        $column_alias = new TDataGridColumn('alias', 'APELIDO', 'left');
        $column_status = new TDataGridColumn('status', 'STATUS', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'CREATED At', 'left');
        $column_updated_at = new TDataGridColumn('Ultima atualização', 'ULTIMA ATUALIZAÇÃO', 'left');

        $column_status->setTransformer(function($value){
            switch ($value) {
                case 0:
                    $class = 'danger';
                    $label = 'INATIVO';
                    break;
                case 1:
                    $class = 'success';
                    $label = 'ATIVO';
                    break;
                
                default:
                    $class = 'secondary';
                    $label = 'Não definido';
                    break;
            }

            $div = new TElement('span');
            $div->class = "btn btn-{$class}";
            $div->style = "text-shadow:none; font-size:12px; font-weight:bold;width:80px;";
            $div->add($label);
            return $div;
        });

        $column_updated_at->setTransformer(function($value){
            return Convert::toDate($value, 'd / m / Y');
        });

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_sku);
        $this->datagrid->addColumn($column_brand);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_alias);
        $this->datagrid->addColumn($column_status);
        // $this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);


        $action1 = new TDataGridAction(['RestaurantProductForm', 'onEdit'], ['id'=>'{id}']);
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
            $object = new Product($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_sku',   NULL);
        TSession::setValue(__CLASS__.'_filter_name',   NULL);
        TSession::setValue(__CLASS__.'_filter_status',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->sku) AND ($data->sku)) {
            $filter = new TFilter('sku', 'like', "%{$data->sku}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_sku',   $filter); // stores the filter in the session
        }


        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_name',   $filter); // stores the filter in the session
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_status',   $filter); // stores the filter in the session
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
            
             
            // veridicando se existe algum no estoque
            $verifyBrand = Brand::where('system_user_id', '=', TSession::getValue('userid'))->first();
            if(empty($verifyBrand)){
                $pos_action = new TAction(['RestaurantBrandList', 'onReload']);
                new TMessage('warning', 'Você precisa cadastrar algumas marcas antes', $pos_action);
            }

            // creates a repository for Product
            $repository = new TRepository('Product');
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


            if (TSession::getValue(__CLASS__.'_filter_sku')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_sku')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_name')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_name')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_status')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status')); // add the session filter
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
            $object = new Product($key, FALSE); // instantiates the Active Record
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
