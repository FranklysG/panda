<?php
/**
 * Exes Active Record
 * @author  <your-name-here>
 */
class Exes extends TRecord
{
    const TABLENAME = 'exes';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;
    private $inventory_id;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('inventory_id');
        parent::addAttribute('description');
        parent::addAttribute('price');
        parent::addAttribute('amount');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $exes->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $exes->system_user->attribute;
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
     * Method set_inventory
     * Sample of usage: $exes->inventory = $object;
     * @param $object Instance of Inventory
     */
    public function set_inventory(Inventory $object)
    {
        $this->inventory = $object;
        $this->inventory_id = $object->id;
    }
    
    /**
     * Method get_inventory
     * Sample of usage: $exes->inventory->attribute;
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
