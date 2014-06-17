<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 13.06.14
 * Time: 11:24
 * To change this template use File | Settings | File Templates.
 */

include_once(__DIR__.'/../ExtendedXmlReader.php');
class CoreTest extends PHPUnit_Framework_TestCase{

        public function testFindNode()
        {
            $reader = new ExtendedXmlReader();

            $reader->open(__DIR__.'/files/test.xml');

            $nodeName = $reader->findNodeByPath(array(
                'catalog',
                'products',
                'product'
            ));

            $this->assertEquals('product',$nodeName);
        }

}