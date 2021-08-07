<?php
/**
 * Payable Active Record
 * @author  <your-name-here>
 */
class Payable extends TRecord
{
    const TABLENAME = 'payable';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $payable;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('name');
        parent::addAttribute('price');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_payable
     * Sample of usage: $payable->payable = $object;
     * @param $object Instance of Payable
     */
    public function set_payable(Payable $object)
    {
        $this->payable = $object;
        $this->payable_id = $object->id;
    }
    
    /**
     * Method get_payable
     * Sample of usage: $payable->payable->attribute;
     * @returns Payable instance
     */
    public function get_payable()
    {
        // loads the associated object
        if (empty($this->payable))
            $this->payable = new Payable($this->payable_id);
    
        // returns the associated object
        return $this->payable;
    }
    


}
