<?php

/**
 * Class Zend_Db_Select
 *
 * @group ZF-378
 */
class Zend_Db_SelectTest extends \PHPUnit_Framework_TestCase
{
    public function adapterProvider()
    {
        return array (
            array ('Mysqli'),
            array ('Pdo_Mysql'),
        );
    }

    /**
     * @dataProvider adapterProvider
     */
    public function testOrderOfSingleFieldWithDirection($adapter)
    {
        $select = $this->getDbAdapter($adapter)->select();
        $select->from(array ('p' => 'product'))
            ->order('productId DESC');

        $expected = 'SELECT `p`.* FROM `product` AS `p` ORDER BY `productId` DESC';
        $this->assertEquals($expected, $select->assemble(),
            'Order direction of field failed');
    }

    /**
     * @dataProvider adapterProvider
     */
    public function testOrderOfMultiFieldWithDirection($adapter)
    {
        $select = $this->getDbAdapter($adapter)->select();
        $select->from(array ('p' => 'product'))
            ->order(array ('productId DESC', 'userId ASC'));

        $expected = 'SELECT `p`.* FROM `product` AS `p` ORDER BY `productId` DESC, `userId` ASC';
        $this->assertEquals($expected, $select->assemble(),
            'Order direction of field failed');
    }

    /**
     * @dataProvider adapterProvider
     */
    public function testOrderOfMultiFieldButOnlyOneWithDirection($adapter)
    {
        $select = $this->getDbAdapter($adapter)->select();
        $select->from(array ('p' => 'product'))
            ->order(array ('productId, userId DESC'));

        $expected = 'SELECT `p`.* FROM `product` AS `p` ORDER BY `productId`, `userId` DESC';
        $this->assertEquals($expected, $select->assemble(),
            'Order direction of field failed');
    }

    /**
     * @dataProvider adapterProvider
     * @group ZF-381
     */
    public function testOrderOfConditionalFieldWithDirection($adapter)
    {
        $select = $this->getDbAdapter($adapter)->select();
        $select->from(array ('p' => 'product'))
            ->order('IF(`productId` > 5,1,0) ASC');

        $expected = 'SELECT `p`.* FROM `product` AS `p` ORDER BY IF(`productId` > 5,1,0) ASC';
        $this->assertEquals($expected, $select->assemble(),
            'Order direction of field failed');
    }

    /**
     * @param string $adapter
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getDbAdapter($adapter = 'Mysqli')
    {
        $config = array (
            'dbname' => ':memory:',
            'host' => '127.0.0.1',
            'username' => 'test',
            'password' => 'test',
        );
        $zendDbAdapter = 'Zend_Db_Adapter_' . ucfirst($adapter);
        return new $zendDbAdapter($config);
    }
}