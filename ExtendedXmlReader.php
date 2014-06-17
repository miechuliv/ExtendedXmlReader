<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 13.06.14
 * Time: 11:16
 * To change this template use File | Settings | File Templates.
 */


class ExtendedXmlReader extends XMLReader{


    /**
     * finds certain xml node by path, eg: catalog > products > product
     * @param $path
     */
    public function findNodeByPath($path)
    {
        $lastElement = $path[count($path)-1];
        foreach($path as $pathElement)
        {
            $foundElement = false;
            while(!$foundElement && $this->next())
            {
                if ($this->nodeType == XMLReader::ELEMENT) {
                    $exp = $this->expand();

                    if ($exp->nodeName == $pathElement)
                    {
                        $foundElement = true;

                    }

                }
            }


            if($foundElement)
            {
                // if not end of path
                if($pathElement != $lastElement)
                {
                    // go one level deeper

                    $this->read();
                    // go to next path element
                    continue;
                }
                else
                {
                    return $exp->nodeName;
                }

            }
        }

        return false;
    }

    public function hasChildNodesXML($node)
    {
        $children = $node->hasChildNodes();

        if($children)
        {
            foreach($node->childNodes as $cnode)
            {
                if($cnode->nodeType == XML_ELEMENT_NODE)
                {
                    return true;
                }
            }
        }

        return false;

    }




}