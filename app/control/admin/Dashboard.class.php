<?php
/**
 * Dashboard
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Franklys Guimaraes
 */
class Dashboard extends TPage
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
            // veridicando se existe algum no estoque
            if(!isset($_GET['debugTunele'])){
                $pos_action = new TAction(['ProductList', 'onReload']);
                new TMessage('warning', 'Estamos cuidando do desenvolvimento, foque na sua area de vendas por enquanto', $pos_action);
            }

            TTransaction::open('app');
            // mapa_reservas semanais
            $repositoy = new TRepository('ViewFinancial');
            $objects = $repositoy->load();

            $html = new THtmlRenderer('app/resources/system_user_dashboard.html');
            
            TTransaction::open('permission');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/info-box.html');
            $indicator6 = new THtmlRenderer('app/resources/info-box.html');
            $indicator7 = new THtmlRenderer('app/resources/info-box.html');
            $indicator8 = new THtmlRenderer('app/resources/info-box.html');
            
            foreach ($objects as $object) {
                $indicator1->enableSection('main', ['title' => 'Vendas hoje',    'icon' => 'cart-arrow-down',       'background' => 'orange', 'value' => $object->sale_today]);
                $indicator2->enableSection('main', ['title' => 'Faturamento Hoje',   'icon' => 'money-bill',      'background' => 'blue',   'value' => Convert::toMonetario($object->sale_cash_today)]);
                $indicator3->enableSection('main', ['title' => 'Faturamento Mês',   'icon' => 'money-bill-wave',      'background' => 'yellow',   'value' => Convert::toMonetario($object->sale_cash_month)]);
                $indicator4->enableSection('main', ['title' => 'Faturamento ano',    'icon' => 'wallet', 'background' => 'purple', 'value' => Convert::toMonetario($object->sale_cash_year)]);
                $indicator5->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'red',  'value' => SystemProgram::count()]);
                $indicator6->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'red',  'value' => SystemProgram::count()]);
                $indicator7->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'red',  'value' => SystemProgram::count()]);
                $indicator8->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'red',  'value' => SystemProgram::count()]);
            }
            $chart = new THtmlRenderer('app/resources/google_column_chart.html');
            $data[] = [ 'Mês', 'Vendas'];
        
            // média de ocupação mensal
            $meses = AppUtil::calendario();
            $objects = Sale::getObjects();
            $data_count = [];
            if($objects){
                foreach ($objects as $key => $value) {
                    if(empty($data_count[date_parse($value->created_at)['month']])){
                        $data_count[date_parse($value->created_at)['month']] = 1 ;
                    }else{
                        $data_count[date_parse($value->created_at)['month']] += 1 ;
                    }
                }
            }
            
            foreach($data_count as $key => $value){
                $data[] = [ Convert::rMes($key),   $value];
            }
            
            // replace the main section variables
            $chart->enableSection('main', array('data'   => json_encode($data),
                                    'width'  => '100%',
                                    'height'  => '300px',
                                    'title'  => 'Fechamento mensal',
                                    'ytitle' => 'Vendas', 
                                    'xtitle' => 'Mês',
                                    'uniqid' => uniqid()));
            
            $html->enableSection('main', ['indicator1' => $indicator1,
                                          'indicator2' => $indicator2,
                                          'indicator3' => $indicator3,
                                          'indicator4' => $indicator4,
                                          'indicator5' => $indicator5,
                                          'indicator6' => $indicator6,
                                          'indicator7' => $indicator7,
                                          'indicator8' => $indicator8,
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
