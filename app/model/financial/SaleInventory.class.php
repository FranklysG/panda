<?php
/**
 * SaleInventory Active Record
 * @author  Franklys Guimaraes
 */
class SaleInventory extends TRecord
{
    const TABLENAME = 'sale_inventory';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;
    private $sale;
    private $inventory;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('sale_id');
        parent::addAttribute('inventory_id');
        parent::addAttribute('price');
        parent::addAttribute('amount');
        parent::addAttribute('discount');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $sale_inventory->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $sale_inventory->system_user->attribute;
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
     * Method set_sale
     * Sample of usage: $sale_inventory->sale = $object;
     * @param $object Instance of Sale
     */
    public function set_sale(Sale $object)
    {
        $this->sale = $object;
        $this->sale_id = $object->id;
    }
    
    /**
     * Method get_sale
     * Sample of usage: $sale_inventory->sale->attribute;
     * @returns Sale instance
     */
    public function get_sale()
    {
        // loads the associated object
        if (empty($this->sale))
            $this->sale = new Sale($this->sale_id);
    
        // returns the associated object
        return $this->sale;
    }
    
    
    /**
     * Method set_inventory
     * Sample of usage: $sale_inventory->inventory = $object;
     * @param $object Instance of Inventory
     */
    public function set_inventory(Inventory $object)
    {
        $this->inventory = $object;
        $this->inventory_id = $object->id;
    }
    
    /**
     * Method get_inventory
     * Sample of usage: $sale_inventory->inventory->attribute;
     * @returns Inventory instance
     */
    public function get_inventory()
    {
        // loads the associated object
        if (empty($this->inventory))
            $this->inventory = new Inventory($this->inventory_id);
    
        // returns the associated object
        return $this->inventory;
    }
    


}
