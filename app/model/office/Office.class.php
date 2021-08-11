<?php
/**
 * Office Active Record
 * @author  <your-name-here>
 */
class Office extends TRecord
{
    const TABLENAME = 'office';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;
    private $office_type;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('description');
        parent::addAttribute('price');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('office_type_id');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $office->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $office->system_user->attribute;
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
     * Method set_office_type
     * Sample of usage: $office->office_type = $object;
     * @param $object Instance of OfficeType
     */
    public function set_office_type(OfficeType $object)
    {
        $this->office_type = $object;
        $this->office_type_id = $object->id;
    }
    
    /**
     * Method get_office_type
     * Sample of usage: $office->office_type->attribute;
     * @returns OfficeType instance
     */
    public function get_office_type()
    {
        // loads the associated object
        if (empty($this->office_type))
            $this->office_type = new OfficeType($this->office_type_id);
    
        // returns the associated object
        return $this->office_type;
    }
    


}
