<?php
/**
 * ViewFinancial Active Record
 * @author  <your-name-here>
 */
class ViewFinancial extends TRecord
{
    const TABLENAME = 'view_financial';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('sale_today');
        parent::addAttribute('sale_cash_today');
        parent::addAttribute('sale_cash_month');
        parent::addAttribute('sale_cash_year');
        parent::addAttribute('payable_cash_month');
        parent::addAttribute('payable_cash_year');
        parent::addAttribute('employee_salary');
    }


}
