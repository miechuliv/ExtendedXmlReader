<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 17.06.14
 * Time: 09:46
 * To change this template use File | Settings | File Templates.
 */


class XmlAnalizer {

    protected $_xmlReader;
    protected $_fileToRead;
    protected $_outputFile;
    // toHtml || toFile
    protected $_outputMode;
    protected $_xmlMap;
    protected $_helperXmlReader;
    private   $_numberOfNodesProcessed;

    public function setHelperXmlReader($helperXmlReader)
    {
        $this->_helperXmlReader = $helperXmlReader;
        return $this;
    }

    public function setOutputMode($outputMode)
    {
        $this->_outputMode = $outputMode;
        return $this;
    }

    function __construct(ExtendedXmlReader $xmlReader)
    {
        $this->_xmlReader = $xmlReader;


    }

    public function setOutputFile($outputFile)
    {
        $this->_outputFile = $outputFile;
        return $this;
    }

    public function setFileToRead($fileToRead)
    {
        $this->_fileToRead = $fileToRead;
        return $this;
    }

    public function initReader()
    {
        if(!file_exists($this->_fileToRead))
        {
            throw new Exception('Unable to load file: '.$this->_fileToRead);
        }

        $this->_xmlReader->open($this->_fileToRead);
        $this->_helperXmlReader->open($this->_fileToRead);
        return $this;
    }

    private function _syncReaders()
    {
        $this->_helperXmlReader->open($this->_fileToRead);
        for($i = 0; $i < $this->_numberOfNodesProcessed;$i++)
        {
            $this->_helperXmlReader->read();
        }
    }

    public function getAllPossibleNodesArray()
    {
        $this->_numberOfNodesProcessed = 0;
        $depth = 1;
        $parents = array();
        $nextSameLevelElement = array();


         while($this->_xmlReader->read())
         {

             $this->_numberOfNodesProcessed ++;

             if($this->_xmlReader->nodeType == XMLReader::ELEMENT)
             {

                 // sync both readers




                 $exp = $this->_xmlReader->expand();
                 $nodeName = $exp->nodeName;
                 $nodeValue = $exp->nodeValue;
                 $hasChildren = $this->_xmlReader->hasChildNodesXML($exp);

                 $this->_syncReaders();
                 if($hasChildren)
                 {

                     do
                     {
                         $result = $this->_helperXmlReader->next();


                     }while($result && $this->_helperXmlReader->nodeType != XMLReader::ELEMENT);

                     if($result)
                     {
                         $nextSameLevelElement[$depth] = $this->_helperXmlReader->expand()->nodeName;
                     }
                 }
                 else
                 {
                     $result = false;
                 }


                 if(isset($parents[$depth-1]) && $parents[$depth-1])
                 {
                     $parentName = $parents[$depth-1];
                 }
                 else
                 {
                     $parentName = false;
                 }



                 if(isset($this->_xmlMap[$nodeName]) && $this->_xmlMap[$nodeName]['parent'] == $parentName)
                // if(isset($this->_xmlMap[$nodeName]))
                 {
                     if(!$hasChildren)
                     {
                         $this->_xmlMap[$nodeName]['values'][] = $nodeValue;
                         // check if next node is one level up
                         if(!$result)
                         {
                             do
                             {
                                 $result = $this->_helperXmlReader->read();


                             }while($result && $this->_helperXmlReader->nodeType != XMLReader::ELEMENT);
                             try{
                                 $nextElement = $this->_helperXmlReader->expand();
                                 $nextName = $nextElement->nodeName;

                                 if($nextName == $nextSameLevelElement[$depth-1])
                                 {
                                     $depth--;
                                 }
                             }catch(Exception $e)
                             {

                             }

                         }

                     }
                     else
                     {
                         $parents[$depth] = $nodeName;
                         $depth ++;
                     }



                     $this->_xmlMap[$nodeName]['counter'] ++ ;
                 }
                 else
                 {

                     if(!$hasChildren)
                     {

                         // check if next node is one level up
                         if(!$result)
                         {
                             do
                             {
                                 $result = $this->_helperXmlReader->read();


                             }while($result && $this->_helperXmlReader->nodeType != XMLReader::ELEMENT);
                             try{
                                 $nextElement = $this->_helperXmlReader->expand();
                                 $nextName = $nextElement->nodeName;

                                 if($nextName == $nextSameLevelElement[$depth-1])
                                 {
                                     $depth--;
                                 }
                             }catch(Exception $e)
                             {

                             }
                         }


                     }
                     else
                     {
                         $parents[$depth] = $nodeName;
                         $depth ++;
                     }

                     $this->_xmlMap[$nodeName] = array(
                         'parent' => $parentName,
                         'counter' => 1,
                         'values' => (!$hasChildren)?array(
                             $nodeValue
                         ):array(),
                         'children' => array(),
                     );
                 }

                 if($parentName && isset($this->_xmlMap[$parentName]) && !in_array($nodeName,$this->_xmlMap[$parentName]['children']))
                 {
                     $this->_xmlMap[$parentName]['children'][] = $nodeName;
                 }

             }
         }
        return $this;
    }

    public function dump()
    {
        var_dump($this->_xmlMap);
    }

    public function getTopLevelNodes()
    {
        $nodes = array();

        foreach($this->_xmlMap as $key => $node)
        {
            if(!$node['parent'])
            {
                $nodes[$key] = $node;
            }
        }

        return $nodes;
    }


    public function toHtml($nodes)
    {
            $html = '<ul>';
            foreach($nodes as $key => $node)
            {
                $html .= '<li>'.$key.'</li>';

                if(!empty($node['children']))
                {
                    $cnodes = array();
                    {
                            foreach($node['children'] as $c)
                            {
                                $cnodes[$c] = $this->_xmlMap[$c];
                            }
                    }

                    $html .= '<li>'.$this->toHtml($cnodes).'</li>';
                }
            }
            $html .= '<ul>';

            return $html;
    }

    public function getXmlMap()
    {
        return $this->_xmlMap;
    }




}