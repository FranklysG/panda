<?php
/**
 * AssistenceSaleList Listing
 * @author  Franklys Guimaraes
 */
class AssistenceSaleList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Sale');
        $this->form->setFormTitle('<strong> BUSQUE SUAS VENDAS</strong>');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new THidden('id');
        TTransaction::open('app');
        $criteria = new TCriteria;
        $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
        foreach ($system_user_unit as $value) {
            $ids[] = $value->system_user_id;
        }
        $criteria->add(new TFilter('system_user_id', 'IN', $ids));
        TTransaction::close();
        $product_id = new TDBUniqueSearch('product_id', 'app', 'ViewInventory', 'product_id', 'product_name', null, $criteria);
        $product_id->setMinLength(1);
        $product_id->setMask('{product_name} : R$ {final_price} ');
        $discount = new TEntry('discount');
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');

        $this->frame = new TElement('div');
        $this->frame->id = 'image_frame';

        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( [ new TLabel('Buscar produto'), $product_id ],
                                [ new TLabel('Criado em'), $created_at ],
                                [ new TLabel('até'), $updated_at ] );
        $row->layout = ['col-sm-4','col-sm-4','col-sm-4'];
        $this->form->addFields( [$this->frame] );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Cadastrar novo', new TAction(['AssistenceSaleForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->enablePopover('Produto', "<img style='max-height: 300px' src='tmp/".TSession::getvalue('userunitid')."/{product_image}'>");       

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'id', 'left');
        $column_system_user_id = new TDataGridColumn('system_user->name', 'RESPONSAVEL', 'left');
        $column_sale_type_id = new TDataGridColumn('sale_type_id', 'FORMA DE PAGAMENTO', 'left');
        $column_product = new TDataGridColumn('{product_name} {description}', 'PRODUTO', 'left');
        $column_quantity = new TDataGridColumn('amount', 'QUANTIDADE', 'left');
        $column_price = new TDataGridColumn('price', 'PREÇO', 'left');
        $column_discount = new TDataGridColumn('discount', 'DESCONTO', 'left');
        $column_total = new TDataGridColumn('= {amount} * ({price} - {discount})', 'TOTAL', 'left');
        $column_time = new TDataGridColumn('created_at', 'HORARIO', 'left');
        $column_created_at = new TDataGridColumn('created_at', 'DATA', 'left');
        $column_updated_at = new TDataGridColumn('updated_at', 'ULTIMA ATUALIZAÇÃO', 'right');
        
        $column_sale_type_id->setTransformer(function($value){
            $class = 'success';
            $label = SaleType::find($value);
            if(!empty($label))
                $label = $label->name;
            $div = new TElement('span');
            $div->class = "btn btn-{$class}";
             $div->style = "text-shadow:none; font-size:12px; font-weight:bold;width:100%;";
            $div->add($label);
            return $div;
        });

        $column_price->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        $column_discount->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        $column_total->setTransformer(function($value){
            return Convert::toMonetario($value);
        });
        
        $column_time->setTransformer(function($value){
            $time = ' AM';
            if(Convert::toDate($value, 'H') > 12){
                $time = ' PM';
            }
            return Convert::toDate($value, 'H:i').$time;
        }); 
        
        $column_updated_at->setTransformer(function($value){
            return Convert::toDate($value, 'd / m / Y');
        });
        
        $column_created_at->setTransformer(function($value){
            return Convert::toDate($value, 'd / m / Y');
        });

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_product);
        $this->datagrid->addColumn($column_price);
        $this->datagrid->addColumn($column_time);
        $this->datagrid->addColumn($column_sale_type_id);
        $this->datagrid->addColumn($column_updated_at);
        // $this->datagrid->addColumn($column_quantity);
        // $this->datagrid->addColumn($column_discount);
        // $this->datagrid->addColumn($column_created_at);


        $action1 = new TDataGridAction(['AssistenceSaleForm', 'onEdit'], ['id'=>'{id}']);
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
            $object = new Sale($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_product_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_discount',   NULL);
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


        if (isset($data->product_id) AND ($data->product_id)) {
            $filter = new TFilter('product_id', '=', $data->product_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_product_id',   $filter); // stores the filter in the session
        }


        if (isset($data->discount) AND ($data->discount)) {
            $filter = new TFilter('discount', 'like', "%{$data->discount}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_discount',   $filter); // stores the filter in the session
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
            $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
            foreach ($system_user_unit as $value) {
                $ids[] = $value->system_user_id;
            }
            $verifyProduct = Product::where('system_user_id', 'IN', $ids)->first();
            $verifyInventory = Inventory::where('system_user_id', 'IN', $ids)->first();
            $verifySale = SaleType::where('system_user_id', 'IN', $ids)->first();
            if(empty($verifyProduct)){
                $pos_action = new TAction(['AssistenceProductList', 'onReload']);
                new TMessage('warning', 'Você precisa cadastrar alguns produtos e adicionalos ao estoque antes', $pos_action);
            }else if(empty($verifyInventory)){
                $pos_action = new TAction(['AssistenceInventoryList', 'onReload']);
                new TMessage('warning', 'Você precisa adicionar alguns produtos ao estoque antes', $pos_action);
            }else if(empty($verifySale)){
                $pos_action = new TAction(['AssistenceSaleTypeFormList', 'onReload']);
                new TMessage('warning', 'Você precisa adicionar algumas formas de pagamentos antes', $pos_action);
            }
            
            // creates a repository for Sale
            $repository = new TRepository('Sale');
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


            if (TSession::getValue(__CLASS__.'_filter_product_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_product_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_discount')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_discount')); // add the session filter
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
                    $object->product_name = null;
                    $object->description = null;
                    $object->price = null;
                    $sale_inventory = SaleInventory::where('sale_id', '=', $object->id)->where('system_user_id', 'IN', $ids)->orderBy('id', 'desc')->load();
                    if(!empty($sale_inventory)){
                        foreach ($sale_inventory as $value) {
                            $object->product_name = $value->inventory->product->name;
                            $object->product_image = $value->inventory->product->image;
                            $object->description = $value->description;
                            $object->price += ($value->amount)*(($value->price)-($value->discount)); 
                        }
                    }
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
            
            // citerios especificos
            $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
            foreach ($system_user_unit as $value) {
                $ids[] = $value->system_user_id;
            }

            // creates a repository for Sale
            $repository = new TRepository('SaleInventory');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('sale_id', '=', $key)); 
            $criteria->add(new TFilter('system_user_id','IN', $ids));
            $objects = $repository->load($criteria, FALSE);
            
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $inventory = Inventory::where('id', '=', $object->inventory_id)->where('status','=',1)->first();
                    if(!empty($inventory)){
                        $inventory->amount_available += $object->amount;
                        $inventory->store(); 
                    }
                    
                    $sale_inventory = SaleInventory::where('sale_id','=',$object->sale_id)->where('inventory_id', '=', $object->inventory_id)->delete();
                }
            }       
            
            $object = new Sale($key, FALSE); // instantiates the Active Record
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
