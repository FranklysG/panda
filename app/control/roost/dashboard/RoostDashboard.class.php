<?php
/**
 * RoostDashboard
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Franklys Guimaraes
 */
class RoostDashboard extends TPage
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
            // mapa_reservas semanais
            $criteria = new TCriteria;
            $criteria->add(new TFilter('id', '=', TSession::getValue('userid')));
            $repositoy = new TRepository('ViewFinancial');
            $objects = $repositoy->load($criteria);

            $html = new THtmlRenderer('app/resources/system_roost_dashboard.html');
            
            TTransaction::open('permission');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/info-box.html');
            $indicator6 = new THtmlRenderer('app/resources/info-box.html');
            $indicator7 = new THtmlRenderer('app/resources/info-box.html');
            $indicator8 = new THtmlRenderer('app/resources/info-box.html');
            $indicator9 = new THtmlRenderer('app/resources/info-box.html');
            $indicator10 = new THtmlRenderer('app/resources/info-box.html');
            
            foreach ($objects as $object) {
                $indicator1->enableSection('main', ['title' => 'Vendas hoje',    'icon' => 'cart-arrow-down',       'background' => 'orange', 'value' => $object->sale_today]);
                $indicator2->enableSection('main', ['title' => 'Faturamento Hoje',   'icon' => 'money-bill',      'background' => 'blue',   'value' => Convert::toMonetario($object->sale_cash_today)]);
                $indicator3->enableSection('main', ['title' => 'Faturamento Mês',   'icon' => 'money-bill-wave',      'background' => 'yellow',   'value' => Convert::toMonetario($object->sale_cash_month)]);
                $indicator4->enableSection('main', ['title' => 'Faturamento ano',    'icon' => 'wallet', 'background' => 'purple', 'value' => Convert::toMonetario($object->sale_cash_year)]);
                $indicator5->enableSection('main', ['title' => 'Despesas Mês', 'icon' => 'handshake',       'background' => 'red',  'value' => Convert::toMonetario($object->exes_cash_month)]);
                $indicator6->enableSection('main', ['title' => 'Despesas Ano', 'icon' => 'handshake',       'background' => 'red',  'value' => Convert::toMonetario($object->exes_cash_year)]);
                $indicator7->enableSection('main', ['title' => 'Contas a pagar Mês', 'icon' => 'handshake',       'background' => 'red',  'value' => Convert::toMonetario($object->payable_cash_month)]);
                $indicator8->enableSection('main', ['title' => 'Contas a pagar Ano', 'icon' => 'handshake',       'background' => 'red',  'value' => Convert::toMonetario($object->payable_cash_year)]);
                $indicator9->enableSection('main', ['title' => 'Lucro esperado Mês', 'icon' => 'wallet',       'background' => 'green',  'value' => Convert::toMonetario(($object->sale_cash_month)-($object->payable_cash_month)-($object->exes_cash_month))]);
                $indicator10->enableSection('main', ['title' => 'Lucro esperado Ano', 'icon' => 'cash-register',       'background' => 'green',  'value' => Convert::toMonetario(($object->sale_cash_year)-($object->payable_cash_year)-($object->exes_cash_year))]);
            }
            
            $chart = new THtmlRenderer('app/resources/google_column_chart.html');
            $data[] = [ 'Mês', 'Vendas'];
        
            // média de ocupação mensal
            $meses = AppUtil::calendario();
            $criteria = new TCriteria;
            $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('userid')));
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
                                          'indicator4' => $indicator4,
                                          'indicator5' => $indicator5,
                                          'indicator6' => $indicator6,
                                          'indicator7' => $indicator7,
                                          'indicator8' => $indicator8,
                                          'indicator9' => $indicator9,
                                          'indicator10' => $indicator10,
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