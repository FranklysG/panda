<?php
/**
 * AssistenceSaleForm Master/Detail
 * @author  Franklys Guimaraes
 */
class AssistenceSaleForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Sale');
        $this->form->setFormTitle('REGISTRO DE VENDAS');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');

        // master fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'name');
        TTransaction::open('app');
        $criteria = new TCriteria;
        $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
        foreach ($system_user_unit as $value) {
            $ids[] = $value->system_user_id;
        }
        $criteria->add(new TFilter('system_user_id', 'IN', $ids));
        TTransaction::close();
        $sale_type_id = new TDBUniqueSearch('sale_type_id', 'app', 'SaleType', 'id', 'name', null, $criteria);
        $sale_type_id->setMinLength(0);
        $sale_type_id->addValidation('Forma de pagamento', new TRequiredValidator);
        $product_id = new TDBUniqueSearch('product_id', 'app', 'Product', 'id', 'system_user_id');
        $product_id->addValidation('Nome do produto', new TRequiredValidator);
        $price = new TEntry('price');
        $price->setNumericMask(2, '.', ',', true);
        $discount = new TEntry('discount');
        $discount->setNumericMask(2, '.', ',', true);
        $description = new TText('description');
        $description->setSize('100%',60);
        $quantity = new TEntry('quantity');
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_system_user_id = new TDBUniqueSearch('detail_system_user_id', 'app', 'SystemUser', 'id', 'name');
        $criteria = new TCriteria;
        TTransaction::open('app');
        $system_user_unit = SystemUserUnit::where('system_unit_id','=', TSession::getValue('userunitid'))->load();
        foreach ($system_user_unit as $value) {
            $ids[] = $value->system_user_id;
        }
        $criteria->add(new TFilter('system_user_id', 'IN', $ids));
        $criteria->add(new TFilter('amount_available', '>=', 0));
        TTransaction::close();
        $detail_inventory_id = new TDBUniqueSearch('detail_inventory_id', 'app', 'ViewInventory', 'product_id', 'product_name',null, $criteria);
        $detail_inventory_id->setMinLength(1);
        $detail_inventory_id->setChangeAction(new TAction([$this, 'onChangeAction']));
        $detail_amount = new TEntry('detail_amount');
        $detail_amount->setValue('1');
        $detail_discount = new TEntry('detail_discount');
        $detail_discount->setValue(0);
        $detail_discount->setNumericMask(2, '.', ',', true);
        $detail_price = new TEntry('detail_price');
        $detail_price->setNumericMask(2, '.', ',', true);
        $detail_final_price = new TEntry('detail_final_price');
        $detail_created_at = new TEntry('detail_created_at');
        $detail_updated_at = new TEntry('detail_updated_at');
        
        // master fields
        $this->form->addFields( [$id] );
        
        // detail fields
        // $this->form->addContent( ['<h4>PRODUTOS</h4><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $this->form->addFields( [new TLabel('PRODUTO'), $detail_inventory_id] );
        $this->form->addFields( [new TLabel('TIPO DE PAGAMENTO') ,$sale_type_id] );
        $row = $this->form->addFields( [new TLabel('QUANTIDADE'), $detail_amount], [new TLabel('PREÇO'), $detail_price] );
        $row->layout = ['col-sm-4', 'col-sm-8'];
        $this->form->addFields( [new TLabel('DESCONTO'), $detail_discount] );
        $this->form->addFields( [new TLabel('OBS:'), $description] );

        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Adicionar produto', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('sale_inventory_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "width:100%;margin-bottom: 10px";
        $this->detail_list->enablePopover('Produto', "<img style='max-height: 300px' src='tmp/".TSession::getvalue('userunitid')."/{product_image}'>");       

        $detail_grid_id = new TDataGridColumn('id', 'Id', 'center');
        $detail_grid_uniqid = new TDataGridColumn('uniqid', 'Uniqid', 'center');
        $detail_grid_system_id = new TDataGridColumn('system_user_id', 'System User Id', 'left');
        $detail_grid_inventory_id = new TDataGridColumn('inventory_id', 'Product Id', 'left');
        $detail_grid_product_name = new TDataGridColumn('product_name', 'PRODUTO', 'left');
        $detail_grid_amount = new TDataGridColumn('amount', 'QTD', 'left');
        $detail_grid_price = new TDataGridColumn('price', 'PREÇO', 'left');
        $detail_grid_discount = new TDataGridColumn('discount', 'DESCONTO', 'left');
        $detail_grid_total = new TDataGridColumn('= {amount} * ({price} - {discount})', 'TOTAL', 'left');

        $detail_grid_price->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        $detail_grid_discount->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        $detail_grid_total->setTransformer(function($value){
            return Convert::toMonetario($value);
        });

        // items
        $this->detail_list->addColumn( $detail_grid_uniqid )->setVisibility(false);
        $this->detail_list->addColumn( $detail_grid_id )->setVisibility(false);
        $this->detail_list->addColumn( $detail_grid_inventory_id )->setVisibility(false);
        $this->detail_list->addColumn( $detail_grid_product_name );
        $this->detail_list->addColumn( $detail_grid_amount );
        $this->detail_list->addColumn( $detail_grid_price );
        $this->detail_list->addColumn( $detail_grid_discount );
        // $this->detail_list->addColumn( $detail_grid_total );

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        if (empty($_GET['id']))
        {
            $id->setEditable(FALSE);
            $detail_price->setEditable(FALSE);
            $this->form->addFields( [$add] );
            $this->form->addAction( 'Salvar',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save blue');
            // $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
            $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        }

        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
         // $this->form->addActionLink('Cadastrar novo',  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction(array($this, 'onClose')), 'fa:times red');

        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
        TScript::create("$('#image_frame').css({'display': 'none'})");
    }

    /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onChangeAction($param)
    {
        try {
            TTransaction::open('app');
            $inventory_id = $param['detail_inventory_id'];
            $object = ViewInventory::where('product_id', '=', $inventory_id)->first();
            $obj = new stdClass;
            $obj->detail_price = $object->final_price;
            if (isset($object->product_id)) {
                $userunitid = TSession::getValue('userunitid');
                $image = Product::find($object->product_id)->image ?? '../product_default_image.png';
                $path = "tmp/{$userunitid}/{$image}";
                TScript::create("$('#image_frame').html('')");
                TScript::create("$('#image_frame').append(\"<img style='max-height: 300px;' src='$path'>\");");
                TScript::create("$('#image_frame').css({'position': 'fixed',
                                                        'top': '7rem',
                                                        'left': '17rem',
                                                        'z-index': '1',
                                                        'background':' #fff',
                                                        'padding': '10px',
                                                        'border': '1px solid #c2c2c2'})");
            }
            
            TForm::sendData('form_Sale', $obj);
            TTransaction::close();
        }catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Add detail item
     * @param $param URL parameters
     */
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();

            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            TTransaction::open('app');
            $product = ViewInventory::where('product_id', '=', $data->detail_inventory_id)->first();
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['inventory_id'] = $product->id;
            $grid_data['product_name'] = $product->product_name;
            $grid_data['product_image'] = $product->product_image;
            $grid_data['amount'] = $data->detail_amount;
            $grid_data['price'] = (!empty($data->detail_price))? $data->detail_price : $product->price;
            $grid_data['discount'] = $data->detail_discount;

            TTransaction::close();
            
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('sale_inventory_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_inventory_id = '';
            $data->detail_price = '';
            $data->detail_discount = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Sale', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit detail item
     * @param $param URL parameters
     */
    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_system_user_id = $param['system_user_id'];
        $data->detail_product_id = $param['product_id'];
        $data->detail_amount = $param['amount'];
        $data->detail_price = $param['price'];
        $data->detail_final_price = $param['final_price'];
        $data->detail_created_at = $param['created_at'];
        $data->detail_updated_at = $param['updated_at'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );
    }
    
    /**
     * Delete detail item
     * @param $param URL parameters
     */
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_system_user_id = '';
        $data->detail_product_id = '';
        $data->detail_amount = '';
        $data->detail_price = '';
        $data->detail_final_price = '';
        $data->detail_created_at = '';
        $data->detail_updated_at = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('sale_inventory_list', $param['uniqid']);
    }
    
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('app');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Sale($key);
                $items  = SaleInventory::where('sale_id', '=', $key)->load();
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $item->product_name = $item->inventory->product->name;
                    $item->product_image = $item->inventory->product->image ?? '../product_default_image.png';
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('app');
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Sale;
            $master->fromArray( (array) $data);
            $master->system_user_id  = TSession::getValue('userid');
            $master->store();
            
            SaleInventory::where('sale_id', '=', $master->id)->delete();
            if(isset($param['sale_inventory_list_uniqid']))
            {
                foreach( $param['sale_inventory_list_uniqid'] as $key => $item_id )
                {
                    $detail = new SaleInventory;
                    $detail->system_user_id  = TSession::getValue('userid');
                    $detail->inventory_id  = $param['sale_inventory_list_inventory_id'][$key];
                    $detail->price  = $param['sale_inventory_list_price'][$key];
                    $detail->amount  = $param['sale_inventory_list_amount'][$key];
                    $detail->discount  = $param['sale_inventory_list_discount'][$key];
                    $detail->sale_id = $master->id;
                    $detail->store();

                    $object = Inventory::where('id', '=', $detail->inventory_id)->where('amount', '>=', $detail->amount)->where('status','=',1)->first();
                    if(!empty($object)){
                        $object->amount_available -= $detail->amount;
                        $object->store();
                    } 
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Sale', (object) ['id' => $master->id]);
            
            $pos_action = new TAction(['AssistenceSaleList', 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $pos_action);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
}
