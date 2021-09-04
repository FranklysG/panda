<?php
/**
 * RoostExesForm Form
 * @author  Franklys Guimaraes
 */
class RoostExesForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Exes');
        $this->form->setFormTitle('CADASTRO DE DESPESAS');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');
        

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'name');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('userunitid')));
        $criteria->add(new TFilter('amount', '>=', 0));
        $inventory_id = new TDBUniqueSearch('inventory_id', 'app', 'ViewInventory', 'id', 'product_id',null, $criteria);
        $inventory_id->setMinLength(0);
        $inventory_id->setMask('{product_name}');
        $change_action = new TAction(array($this, 'onChangeAction'));
        $inventory_id->setChangeAction($change_action);
        $amount = new TEntry('amount');
        $amount->forceUpperCase();
        $description = new TEntry('description');
        $description->forceUpperCase();
        $description->addValidation('Nome do produto', new TRequiredValidator);
        $price = new TEntry('price');
        $price->setNumericMask(2, '.', ',', true);
        $price->addValidation('Preço do produto', new TRequiredValidator);
        $created_at = new TDate('created_at');
        $created_at->addValidation('Data da despesa', new TRequiredValidator);
        $updated_at = new TEntry('updated_at');


        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( 
                                [ new TLabel('Produto'), $inventory_id ],
                                [ new TLabel('<br />Quantidade'), $amount ],
                                [ new TLabel('<br />Descrição'), $description ],
                                [ new TLabel('<br />Preço'), $price ],
                                [ new TLabel('<br />Data da despesa'), $created_at ]
                            );
        
        $row->layout = ['col-sm-12','col-sm-12','col-sm-12','col-sm-12','col-sm-12'];
        

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
      
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addActionLink(_t('Back'),  new TAction(['ProductList', 'onReload']), 'fa:arrow-circle-left red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction(array($this, 'onClose')), 'fa:times red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

     /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onChangeAction($param)
    {
        try {
            TTransaction::open('app');
            $inventory_id = $param['inventory_id'];
            $object = Inventory::where('id', '=', $inventory_id)->first();
            $obj = new stdClass;
            $obj->price = $object->final_price;
            if (isset($object->product_image)) {
                $userid = TSession::getValue('userid');
                $path = "tmp/{$userid}/{$object->product_image}";
                TScript::create("$('#image_frame').html('')");
                TScript::create("$('#image_frame').append(\"<img style='max-height: 300px' src='$path'>\");");
            }
            
            TForm::sendData('form_Exes', $obj);
            TTransaction::close();
        }catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
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
            
            $object = new Exes;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->system_user_id = TSession::getValue('userid');
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            $object = Inventory::where('id', '=', $data->inventory_id)->where('amount', '>=', $data->amount)->where('status','=',1)->first();
            if(!empty($object)){
                $object->amount_available -= $data->amount;
                $object->store();
            } 
            
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['RoostExesList', 'onReload']));
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
                $object = new Exes($key); // instantiates the Active Record
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
