<?php
/**
 * RoostOfficeForm Form
 * @author  Franklys Guimaraes
 */
class RoostOfficeForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Office');
        $this->form->setFormTitle('CADASTRO DE SERVIÇO');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');
        

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'name');
        $office_type_id = new TDBUniqueSearch('office_type_id', 'app', 'OfficeType', 'id', 'name');
        $office_type_id->setMinLength(1);
        $description = new TEntry('description');
        $description->forceUpperCase();
        $description->addValidation('Descrição', new TRequiredValidator);
        $price = new TEntry('price');
        $price->setNumericMask(2, '.', ',', true);
        $price->addValidation('Preço', new TRequiredValidator);
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ new TLabel('TIPO DE SERVIÇO'), $office_type_id ] );
        $this->form->addFields( [ new TLabel('<br />Descrição'), $description ] );
        $this->form->addFields( [ new TLabel('<br />Preço'), $price ] );

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
            
            $object = new Office;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->system_user_id = TSession::getValue('userid');
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['RoostOfficeList', 'onReload']));
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
                $object = new Office($key); // instantiates the Active Record
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
