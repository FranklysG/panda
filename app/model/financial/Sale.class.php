<?php
/**
 * Sale Active Record
 * @author  <your-name-here>
 */
class Sale extends TRecord
{
    const TABLENAME = 'sale';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('sale_type_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $sale->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $sale->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    
    
    /**
     * Method set_sale_type
     * Sample of usage: $sale->sale_type = $object;
     * @param $object Instance of SaleType
     */
    public function set_sale_type(SaleType $object)
    {
        $this->sale_type = $object;
        $this->sale_type_id = $object->id;
    }
    
    /**
     * Method get_sale_type
     * Sample of usage: $sale->sale_type->attribute;
     * @returns SaleType instance
     */
    public function get_sale_type()
    {
        // loads the associated object
        if (empty($this->sale_type))
            $this->sale_type = new SaleType($this->sale_type_id);
    
        // returns the associated object
        return $this->sale_type;
    }
    
}
