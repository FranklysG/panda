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
            $html = new THtmlRenderer('app/resources/system_user_dashboard.html');
            
            TTransaction::open('permission');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/info-box.html');
            $indicator6 = new THtmlRenderer('app/resources/info-box.html');
            $indicator7 = new THtmlRenderer('app/resources/info-box.html');
            
            $indicator1->enableSection('main', ['title' => 'Faturamento dia',    'icon' => 'user',       'background' => 'orange', 'value' => SystemUser::count()]);
            $indicator2->enableSection('main', ['title' => 'Faturamento mês',   'icon' => 'users',      'background' => 'blue',   'value' => SystemGroup::count()]);
            $indicator3->enableSection('main', ['title' => 'Faturamento ano',    'icon' => 'university', 'background' => 'purple', 'value' => SystemUnit::count()]);
            $indicator4->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'green',  'value' => SystemProgram::count()]);
            $indicator5->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'green',  'value' => SystemProgram::count()]);
            $indicator6->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'green',  'value' => SystemProgram::count()]);
            $indicator7->enableSection('main', ['title' => 'Contas a pagar', 'icon' => 'code',       'background' => 'green',  'value' => SystemProgram::count()]);
            
            $chart = new THtmlRenderer('app/resources/google_column_chart.html');
            $data[] = [ 'Mês', 'Vendas'];
        
            // média de ocupação mensal
            $meses = AppUtil::calendario();
            $objects = Inventory::getObjects();
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
