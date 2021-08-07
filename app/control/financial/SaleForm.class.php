<?php
/**
 * SaleForm Form
 * @author  <your name here>
 */
class SaleForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Inventory');
        $this->form->setFormTitle('REALIZAR NOVA VENDA');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');

        // create the form fields
        $id = new THidden('id');
        $product_id = new TDBUniqueSearch('product_id', 'app', 'ViewInventory', 'product_id', 'product_name');
        $product_id->setMinLength(1);
        $product_id->setMask('{product_name} : R$ {final_price} ');
        $product_id->addValidation('Nome do produto', new TRequiredValidator);
        $price = new TEntry('price');
        $price->setNumericMask(2, '.', ',', true);
        $price->addValidation('Preço do produto', new TRequiredValidator);
        $quantity = new TEntry('quantity');
        $discount = new TEntry('discount');
        $discount->setNumericMask(2, '.', ',', true);
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');

        // set exit action for input_exit
        $change_action = new TAction(array($this, 'onChangeAction'));
        $product_id->setChangeAction($change_action);

        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields(
                                [ new TLabel('<br />Buscar produto'), $product_id ],
                                [ new TLabel('<br />Preço produto'), $quantity],
                                [ new TLabel('<br />Preço produto'), $price ],
                                [ new TLabel('<br />Disconto na venda'), $discount ]
                            );
        $row->layout = ['col-sm-12','col-sm-12','col-sm-12','col-sm-12','col-sm-12'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
            $price->setEditable(FALSE);
        }
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction(array($this, 'onClose')), 'fa:times red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

   /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onChangeAction($param)
    {
        try {
            TTransaction::open('app');
            $product_id = $param['product_id'];
            $object = ViewInventory::where('product_id', '=', $product_id)->first();
            $obj = new stdClass;
            $obj->price = $object->final_price;
            TForm::sendData('form_Inventory', $obj);
            TTransaction::close();
        }catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('app'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new Sale;  // create an empty object
            $object->system_user_id = TSession::getValue('userid');
            $object->fromArray( (array) $data); // load the object with data
            $object->price = $data->price;
            $object->discount = $data->discount;
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['SaleList', 'onReload']));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('app'); // open a transaction
                $object = new Sale($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
