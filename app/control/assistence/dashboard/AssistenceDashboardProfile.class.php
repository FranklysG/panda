<?php
/**
 * AssistenceDashboardProfile
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Franklys Guimaraes
 */
class AssistenceDashboardProfile extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        try
        {
           
            TTransaction::open('app');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('id','=', TSession::getValue('userid')));
            $repositoy = new TRepository('ViewFinancial');
            $objects = $repositoy->load($criteria);

            $html = new THtmlRenderer('app/resources/assistence/system_assistence_dashboard_profile.html');
            
            TTransaction::open('permission');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');

            $sale_today = 0;
            $sale_cash_month = 0;
            foreach ($objects as $object) {
                $sale_today += $object->sale_today;
                $sale_cash_month += $object->sale_cash_month;
                $indicator1->enableSection('main', ['title' => 'Vendas hoje',    'icon' => 'cart-arrow-down',       'background' => 'orange', 'value' => $sale_today]);
                $indicator2->enableSection('main', ['title' => 'Faturamento Mês',   'icon' => 'money-bill-wave',      'background' => 'blue',   'value' => Convert::toMonetario($sale_cash_month)]);
                $indicator3->enableSection('main', ['title' => 'Comissão',   'icon' => 'handshake',      'background' => 'purple',   'value' => Convert::toMonetario($sale_cash_month*0.04)]);
            }
            
            $chart = new THtmlRenderer('app/resources/google_column_chart.html');
            $data[] = [ 'Mês', 'Vendas'];
        
            // média de ocupação mensal
            $meses = AppUtil::calendario();
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id','=', TSession::getValue('userid')));
            $criteria->add(new TFilter('date(created_at)', 'BETWEEN', date('Y-m-d', strtotime('-1 week')), date('Y-m-d')));
            
            $repositoy = new TRepository('Sale');
            $objects = $repositoy->load($criteria, false);

            $data_count = [];
            if($objects){
                foreach ($objects as $key => $value) {
                    if(empty($data_count[date_parse($value->created_at)['day'].'/'.date_parse($value->created_at)['month']])){
                        $data_count[date_parse($value->created_at)['day'].'/'.date_parse($value->created_at)['month']] = 1 ;
                    }else{
                        $data_count[date_parse($value->created_at)['day'].'/'.date_parse($value->created_at)['month']] += 1 ;
                    }
                }
            }
            
            foreach($data_count as $key => $value){
                $day = explode('/',$key)[0];
                $month = explode('/',$key)[1];
                $data[] = [ $day.'/'.Convert::rMes($month),   $value];
            }
            
            // replace the main section variables
            $chart->enableSection('main', array('data'   => json_encode($data),
                                    'width'  => '100%',
                                    'height'  => '300px',
                                    'title'  => 'Fechamento mensal',
                                    'ytitle' => 'Vendas por dia', 
                                    'xtitle' => 'Dia',
                                    'uniqid' => uniqid()));
            
            $html->enableSection('main', ['indicator1' => $indicator1,
                                          'indicator2' => $indicator2,
                                          'indicator3' => $indicator3,
                                          'chart'     => $chart] );
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            
            parent::add($container);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            parent::add($e->getMessage());
        }
    }
}
