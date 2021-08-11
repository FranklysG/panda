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
    private $product;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('sale_type_id');
        parent::addAttribute('product_id');
        parent::addAttribute('price');
        parent::addAttribute('discount');
        parent::addAttribute('quantity');
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
     * Method set_product
     * Sample of usage: $sale->product = $object;
     * @param $object Instance of Product
     */
    public function set_product(Product $object)
    {
        $this->product = $object;
        $this->product_id = $object->id;
    }
    
    /**
     * Method get_product
     * Sample of usage: $sale->product->attribute;
     * @returns Product instance
     */
    public function get_product()
    {
        // loads the associated object
        if (empty($this->product))
            $this->product = new Product($this->product_id);
    
        // returns the associated object
        return $this->product;
    }
    


}
