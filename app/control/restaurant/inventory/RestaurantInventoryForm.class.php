<?php
/**
 * RestaurantInventoryForm Form
 * @author  Franklys Guimaraes
 */
class RestaurantInventoryForm extends TPage
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
        $this->form->setFormTitle('CADASTRO DE ESTOQUE');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');
        

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
        $product_id = new TDBUniqueSearch('product_id', 'app', 'Product', 'id', 'name', null, $criteria);
        $product_id->setMinLength(1);
        $product_id->addValidation('Nome do produto', new TRequiredValidator);
        $amount_available = new TEntry('amount_available');
        $amount_available->addValidation('Quantidade', new TRequiredValidator);
        $amount = new THidden('amount');
        $price = new TEntry('price');
        $price->setNumericMask(2, ',', '.', true);
        $price->addValidation('Preço do produto', new TRequiredValidator);
        $final_price = new TEntry('final_price');
        $final_price->setNumericMask(2, ',', '.', true);
        $final_price->addValidation('Preço final', new TRequiredValidator);
        $status = new TCombo('status');
        $status->addItems(
            [
                '1' => 'ATIVO',
                '0' => 'INATIVO'
            ]
        );
        $status->setDefaultOption(false);
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');


        // add the fields
        $this->form->addFields( [ $id, $amount ] );
        $row = $this->form->addFields( [ new TLabel('Buscar produto (nome)'), $product_id ],
                                [ new TLabel('<br />Quantidade'), $amount_available ],
                                [ new TLabel('<br />Preço de custo'), $price ],
                                [ new TLabel('<br />Preço de venda'), $final_price ],
                                [ new TLabel('<br />Subtrair estoque'), $status ]
                            );
        $row->layout = ['col-sm-12','col-sm-12','col-sm-12','col-sm-12','col-sm-12'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addActionLink('Cadastrar novo',  new TAction([$this, 'onEdit']), 'fa:eraser red');
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
            
            $object = new Inventory;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->system_user_id = TSession::getValue('userid');
            $object->amount = $data->amount_available;
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['RestaurantInventoryList', 'onReload']));
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
                $object = new Inventory($key); // instantiates the Active Record
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
