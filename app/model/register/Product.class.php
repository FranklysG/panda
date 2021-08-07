<?php
/**
 * Product Active Record
 * @author  <your-name-here>
 */
class Product extends TRecord
{
    const TABLENAME = 'product';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;
    private $inventorys;
    private $sales;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('sku');
        parent::addAttribute('name');
        parent::addAttribute('alias');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $product->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $product->system_user->attribute;
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
     * Method addInventory
     * Add a Inventory to the Product
     * @param $object Instance of Inventory
     */
    public function addInventory(Inventory $object)
    {
        $this->inventorys[] = $object;
    }
    
    /**
     * Method getInventorys
     * Return the Product' Inventory's
     * @return Collection of Inventory
     */
    public function getInventorys()
    {
        return $this->inventorys;
    }
    
    /**
     * Method addSale
     * Add a Sale to the Product
     * @param $object Instance of Sale
     */
    public function addSale(Sale $object)
    {
        $this->sales[] = $object;
    }
    
    /**
     * Method getSales
     * Return the Product' Sale's
     * @return Collection of Sale
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->inventorys = array();
        $this->sales = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related Inventory objects
        $repository = new TRepository('Inventory');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('product_id', '=', $id));
        $this->inventorys = $repository->load($criteria);
    
        // load the related Sale objects
        $repository = new TRepository('Sale');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('product_id', '=', $id));
        $this->sales = $repository->load($criteria);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related Inventory objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('product_id', '=', $this->id));
        $repository = new TRepository('Inventory');
        $repository->load($criteria);
        
        // store the related Inventory objects
        if ($this->inventorys)
        {
            foreach ($this->inventorys as $inventory)
            {
                unset($inventory->id);
                $inventory->product_id = $this->id;
                $inventory->store();
            }
        }
        // delete the related Sale objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('product_id', '=', $this->id));
        $repository = new TRepository('Sale');
        $repository->delete($criteria);
        // store the related Sale objects
        if ($this->sales)
        {
            foreach ($this->sales as $sale)
            {
                unset($sale->id);
                $sale->product_id = $this->id;
                $sale->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related Inventory objects
        $repository = new TRepository('Inventory');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('product_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the related Sale objects
        $repository = new TRepository('Sale');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('product_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
