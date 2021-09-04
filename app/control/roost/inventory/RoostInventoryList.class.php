<?php
/**
 * RoostInventoryList Listing
 * @author  Franklys Guimaraes
 */
class RoostInventoryList extends TPage
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
        $this->form->setFormTitle('<strong> BUSQUE SEUS PRODUTOS NO ESTOQUE</strong>');
        $this->form->setFieldSizes('100%');
         

        // create the form fields
        $id = new THidden('id');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('userunitid')));
        $product_id = new TDBUniqueSearch('product_id', 'app', 'ViewInventory', 'product_id', 'product_name', null, $criteria);
        $product_id->setMinLength(1);
        $product_id->setMask('{product_name}');
        $amount = new TEntry('amount');
        $price = new TEntry('price');
        $final_price = new TEntry('final_price');
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');

        $criteria = new TCriteria;
        TTransaction::open('app');
        $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
        
        foreach ($system_user_unit as $value) {
            $ids[] = $value->system_user_id;
        }
        $criteria->add(new TFilter('system_user_id','IN', $ids));
        $repositoy = new TRepository('Inventory');
        $objects = $repositoy->load($criteria);
        TTransaction::close();
        $inventory_price = null;
        $inventory_final_price = null;
        $inventory_lucro = null;
        foreach ($objects as $key => $object) {
            $inventory_price += ($object->price * $object->amount);
            $inventory_final_price += ($object->final_price * $object->amount);
        }
        $inventory_lucro = ($inventory_final_price - $inventory_price);

        $html = new THtmlRenderer('app/resources/roost/system_inventory_dashboard.html');
        $indicator = new THtmlRenderer('app/resources/info-box.html');
        $indicator1 = new THtmlRenderer('app/resources/info-box.html');
        $indicator2 = new THtmlRenderer('app/resources/info-box.html');
        $indicator->enableSection('main', ['title' => 'MONTANTE INVESTIDO',    'icon' => 'cart-arrow-down',       'background' => 'light-blue', 'value' => Convert::toMonetario($inventory_price)]);
        $indicator1->enableSection('main', ['title' => 'MONTANTE NO ESTOQUE',    'icon' => 'cart-arrow-down',       'background' => 'yellow', 'value' => Convert::toMonetario($inventory_final_price)]);
        $indicator2->enableSection('main', ['title' => 'LUCRO ESPERADO DO ESTOQUE',    'icon' => 'cart-arrow-down',       'background' => 'green', 'value' => Convert::toMonetario($inventory_lucro)]);
        $html->enableSection('main', [
            'indicator' => $indicator,
            'indicator1' => $indicator1,
            'indicator2' => $indicator2
        ] );
        
        // add the fields
        $this->form->addFields( [ $id ]);
        $this->form->addFields( 
                                [ new TLabel('Nome do Produto'), $product_id ] ,
                                [ new TLabel('Preço de venda'), $final_price ] ,
                                [ new TLabel('Criado em'), $created_at ] ,
                                [ new TLabel('até'), $updated_at ] 
                            );


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Cadastrar novo', new TAction(['RoostInventoryForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_product_name = new TDataGridColumn('product_name', 'PRODUTO', 'left');
        $column_product_sku = new TDataGridColumn('product_sku', 'SKU', 'left');
        $column_amount = new TDataGridColumn('amount', 'QTD. COMPRADA', 'left');
        $column_amount_available = new TDataGridColumn('amount_available', 'QTD. DISPONIVEL', 'left');
        $column_price = new TDataGridColumn('price', 'PREÇO MEDIO', 'left');
        $column_final_price = new TDataGridColumn('final_price', 'PREÇO DE VENDA', 'left');
        $column_total = new TDataGridColumn('= {amount} * {final_price}', 'TOTAL', 'left');
        $column_status = new TDataGridColumn('status', 'SUBTRAIR ESTOQUE', 'center');
        $column_created_at = new TDataGridColumn('created_at', 'Created At', 'left');
        $column_updated_at = new TDataGridColumn('updated_at', 'ATUALIZADO', 'right');
        
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
            }

            $div = new TElement('span');
            $div->class = "btn btn-{$class}";
             $div->style = "text-shadow:none; font-size:12px; font-weight:bold;width:100%;";
            $div->add($label);
            return $div;
        });

        $column_price->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        $column_final_price->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        $column_total->setTransformer(function($value){
            return Convert::toMonetario($value);
        });
        
        $column_updated_at->setTransformer(function($value){
            return Convert::toDate($value, 'd / m / Y');
        });

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_product_sku);
        $this->datagrid->addColumn($column_product_name);
        $this->datagrid->addColumn($column_amount);
        $this->datagrid->addColumn($column_amount_available);
        $this->datagrid->addColumn($column_price);
        $this->datagrid->addColumn($column_final_price);
        $this->datagrid->addColumn($column_total);
        $this->datagrid->addColumn($column_status);
        // $this->datagrid->addColumn($column_updated_at);
        
        
        $action1 = new TDataGridAction(['RoostInventoryForm', 'onEdit'], ['id'=>'{id}']);
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
        $container->add($html);
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
            $object = new Inventory($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_product_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_amount',   NULL);
        TSession::setValue(__CLASS__.'_filter_price',   NULL);
        TSession::setValue(__CLASS__.'_filter_final_price',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);
        TSession::setValue(__CLASS__.'_filter_updated_at',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->product_id) AND ($data->product_id)) {
            $filter = new TFilter('product_id', '=', $data->product_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_product_id',   $filter); // stores the filter in the session
        }


        if (isset($data->amount) AND ($data->amount)) {
            $filter = new TFilter('amount', 'like', "%{$data->amount}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_amount',   $filter); // stores the filter in the session
        }


        if (isset($data->price) AND ($data->price)) {
            $filter = new TFilter('price', 'like', "%{$data->price}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_price',   $filter); // stores the filter in the session
        }


        if (isset($data->final_price) AND ($data->final_price)) {
            $filter = new TFilter('final_price', 'like', "%{$data->final_price}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_final_price',   $filter); // stores the filter in the session
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
            $verifyProduct = Product::where('system_user_id', '=', TSession::getValue('userunitid'))->first();
            if(empty($verifyProduct)){
                $pos_action = new TAction(['RoostProductList', 'onReload']);
                new TMessage('warning', 'Você precisa cadastrar alguns produtos e adicionalos ao estoque antes', $pos_action);
            }

            // creates a repository for Inventory
            $repository = new TRepository('ViewInventory');
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


            if (TSession::getValue(__CLASS__.'_filter_product_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_product_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_amount')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_amount')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_price')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_price')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_final_price')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_final_price')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_created_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_created_at')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_updated_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_updated_at')); // add the session filter
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
            $key = $param['key']; // get the parameter $key
            TTransaction::open('app'); // open a transaction with database
            
            $object = new Inventory($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            // new TMessage('error', $e->getMessage()); // shows the exception error message
            new TMessage('warning', 'Existem vendas com esse produto cadastrado, exclua primeiro'); // shows the exception error message
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
