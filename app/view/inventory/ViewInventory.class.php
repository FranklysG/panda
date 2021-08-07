<?php
/**
 * ViewInventory Active Record
 * @author  <your-name-here>
 */
class ViewInventory extends TRecord
{
    const TABLENAME = 'view_inventory';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('product_id');
        parent::addAttribute('product_name');
        parent::addAttribute('amount');
        parent::addAttribute('price');
        parent::addAttribute('final_price');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }


}