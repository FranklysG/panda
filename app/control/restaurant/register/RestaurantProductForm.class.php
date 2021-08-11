<?php
/**
 * RestaurantProductForm Form
 * @author  Franklys Guimaraes
 */
class RestaurantProductForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Product');
        $this->form->setFormTitle('CADASTRO DE PRODUTO');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');
        

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'name');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('userid')));
        $brand_id = new TDBUniqueSearch('brand_id', 'app', 'Brand', 'id', 'name', null, $criteria);
        $brand_id->setMinLength(1);
        $brand_id->addValidation('Marca', new TRequiredValidator);
        $sku = new TEntry('sku');
        $name = new TEntry('name');
        $name->forceUpperCase();
        $name->addValidation('Nome do produto', new TRequiredValidator);
        $alias = new TEntry('alias');
        $alias->forceUpperCase();
        $alias->addValidation('Apelido do produto', new TRequiredValidator);
        $image = new TFile('image', TSession::getValue('userid'));
        $image->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );
        $image->addValidation('Imagem do produto', new TRequiredValidator);
        $status = new TCombo('status');
        $status->addItems(
            [
                '1' => 'ATIVO',
                '0' => 'INATIVO'
            ]
        );
        $status->setDefaultOption(false);

        $status->addValidation('Status', new TRequiredValidator);
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');

        $this->frame = new TElement('div');
        $this->frame->id = 'image_frame';
        $this->frame->style = 'width:100px;height:auto;;border:1px solid gray;padding:4px;';


        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( 
                                [ new TLabel('Sku'), $sku ],
                                [ new TLabel('<br />Marca'), $brand_id ],
                                [ new TLabel('<br />Nome'), $name ],
                                [ new TLabel('<br />Apelido'), $alias ],
                                [ new TLabel('<br />Imagem do produto'), $image ],
                                [ new TLabel('<br />'), $this->frame ],
                                [ new TLabel('<br />Status'), $status ]);

        $row->layout = ['col-sm-12','col-sm-12','col-sm-12','col-sm-12','col-sm-12','col-sm-12','col-sm-12'];
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
            $sku->setEditable(FALSE);
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
            $object = new Product;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            if(empty($data->sku) and Product::where('sku','!=',$data->sku)->load()){
                $object->sku = AppUtil::hash(8);
            }
            $object->system_user_id = TSession::getValue('userid');
            $object->store(); // save the object

            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['ProductList', 'onReload']));
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
                $userid = TSession::getValue('userid');
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('app'); // open a transaction
               
                $object = new Product($key); // instantiates the Active Record
                if (isset($object->image)) {
                    $image = new TImage("tmp/{$userid}/{$object->image}");
                    $image->style = 'width: 100%';
                    $this->frame->add($image);
                }
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

    public static function onComplete($param)
    {
        $userid = TSession::getValue('userid');
        // refresh photo_frame
        $path = PATH."/tmp/{$userid}/{$param['image']}";
        TScript::create("$('#image_frame').html('')");
        TScript::create("$('#image_frame').append(\"<img style='width:100%' src='$path'>\");");
    }
}
