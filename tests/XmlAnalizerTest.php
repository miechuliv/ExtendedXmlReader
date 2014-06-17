<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 17.06.14
 * Time: 10:27
 * To change this template use File | Settings | File Templates.
 */

include_once(__DIR__.'/../XmlAnalizer.php');
include_once(__DIR__.'/../ExtendedXmlReader.php');

class XmlAnalizerTest extends PHPUnit_Framework_TestCase {

    public function testMapSimpleFile()
    {
        $reader = new ExtendedXmlReader();
        $reader2 = new ExtendedXmlReader();
        $analizer = new XmlAnalizer($reader);

        $topNodes = $analizer->setFileToRead(__DIR__.'/files/test.xml')
        ->setHelperXmlReader($reader2)
        ->initReader()
        ->getAllPossibleNodesArray()
        ->getTopLevelNodes();

        $html = $analizer->toHtml($topNodes);

        $this->assertNotEmpty($html);
    }
}
