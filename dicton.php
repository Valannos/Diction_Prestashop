<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dicton extends Module {

    function __construct() {

        $this->name = 'dicton';
        $this->tab = 'front_office_features';
        $this->version = '1.1';
        $this->author = 'R. Vanel';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Today\'s proverb');
        $this->description = $this->l('Displays a proverb depending on current date');
        $this->confirmUninstall = $this->l('Are you sure you want to remove Dicton ?');
    }

    public function install() {

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        Configuration::updateValue('DICTON_NAME', 'dicton');
        /* TABLE IS CREATED UPPON INSTALLING MODULE */
        $name = Configuration::get('DICTON_NAME');
        $today = getdate();

        $sql_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . $name . '` (
               
                `month` INT(11) NOT NULL,
                `day` INT(11) NOT NULL,
                `saint` TEXT,
                `proverb` TEXT,
                `advice` TEXT,
                `gender` INT(11)
                        
                
                ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARACTER SET = utf8';

        $isCreated = Db::getInstance()->execute($sql_table);
        /* INSERTION OF VARIOUS EXEMPLES OF PROVERBS, NOTE THAT THE FIRST ONE IS ARBITRARILY ATTRIBUTED TO CURRENT DATE */
        if ($isCreated) {
            Db::getInstance()->insert($name, array(
                'month' => $today['mon'],
                'day' => $today['mday'],
                'saint' => "Eloi",
                'proverb' => "Si à la Saint-éloi tu brûles ton bois, tu auras froid pendant trois mois.",
                'advice' => "Commencez à vous équiper pour l\'hiver",
                'gender' => 0
            ));

            Db::getInstance()->insert($name, array(
                'month' => 5,
                'day' => 21,
                'saint' => "Jean",
                'proverb' => "A la Saint-Jean, les feux sont grands",
                'advice' => "Equipez votre jardin pour l\'été !",
                'gender' => 0
            ));

            Db::getInstance()->insert($name, array(
                'month' => 5,
                'day' => 22,
                'saint' => "Thérèse",
                'proverb' => "Souvent Sainte-Thérèse nous apporte un petit été",
                'advice' => "Equipez votre jardin pour l\'été !",
                'gender' => 1
            ));
        }








        return
                parent::install() &&
                $this->registerHook('leftColumn') &&
                $this->registerHook('header') &&
                $isCreated


        ;
    }

    public function uninstall() {
        $name = Configuration::get('DICTON_NAME');
        $sql = 'DROP TABLE `' . _DB_PREFIX_ . $name . '`';
        $isDeleted = Db::getInstance()->execute($sql);
        Configuration::deleteByName('DICTON_NAME');
        return parent::uninstall() &&
                $isDeleted;
    }

    public function hookDisplayLeftColumn() {
        /* GET CURRENT DAY DATE INFORMATION */

        $currentDate = getdate();
        $currentDayNum = $currentDate['mday'];
        $currentMonthNum = $currentDate['mon'];


        /* PREPARING SQL QUERY USING DAY AND MONTH NUMBER AS CONDITIONS TO FETCH APPROPRIATE ROW */

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'dicton WHERE month = ' . $currentMonthNum . ' AND day = ' . $currentDayNum;
        $row = Db::getInstance()->getRow($sql);
        /* IF NO JARDITOU ADVICE WAS RECOVERED, A DEFAULT MESSAGE WILL BE RETURNED */
        if (empty($row['advice'])) {
            $row['advice'] = 'Bonne journée sur Jarditou !';
        }

        /* DATA FETCHED FROM DATABASE ARE SENT TO SMARTY TEMPLATE */

        $this->context->smarty->assign(
                array(
                    'saint' => $row['saint'],
                    'today_proverb' => $row['proverb'],
                    'today_advice' => $row['advice'],
                    'gender' => $row['gender']
                )
        );
        return $this->display(__FILE__, 'dicton.tpl');
    }

    /* ADDITION OF CSS */

    public function hookDisplayHeader() {
        $this->context->controller->addCSS($this->_path . 'views/css/dicton.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/font-awesome-4.7.0/css/font-awesome.min.css', 'all');
    }

}
