<?php
    class sxml
    { 
    var $parser;   
    var $srcenc;   
    var $dstenc;   
    var $_struct = array();   

    function SofeeXmlParser($srcenc = null, $dstenc = null) {   
  $this->srcenc = $srcenc;   
  $this->dstenc = $dstenc;   
     
  $this->parser = null;   
  $this->_struct = array();   
    }   

    function free() {   
  if (isset($this->parser) && is_resource($this->parser)) {   
      xml_parser_free($this->parser);   
      unset($this->parser);   
  }   
    }   

    function parseFile($file) {   
  $data = @file_get_contents($file);
  if(!$data){
	  return false;
  }
  $this->parseString($data);   
    }   
  
    function parseString($data) {   
  if ($this->srcenc === null) {   
      $this->parser = @xml_parser_create() or die('Unable to create XML parser resource.');   
  } else {   
      $this->parser = @xml_parser_create($this->srcenc) or die('Unable to create XML parser resource with '. $this->srcenc .' encoding.');   
  }   
     
  if ($this->dstenc !== null) {   
      @xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $this->dstenc) or die('Invalid target encoding');   
  }   
  xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
  xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1); 
  if (!xml_parse_into_struct($this->parser,$data,$this->_struct)) {   
      printf("XML error: %s at line %d",    
        xml_error_string(xml_get_error_code($this->parser)),    
        xml_get_current_line_number($this->parser)   
      );   
      $this->free();   
      exit();   
  }   
     
  $this->_count = count($this->_struct);   
  $this->free();   
    }   

    function getTree() {   
  $i = 0;   
  $tree = array();   
  
  $tree = $this->addNode(   
      $tree,    
      $this->_struct[$i]['tag'],    
      (isset($this->_struct[$i]['value'])) ? $this->_struct[$i]['value'] : '',    
      (isset($this->_struct[$i]['attributes'])) ? $this->_struct[$i]['attributes'] : '',    
      $this->getChild($i)   
  );   
  
  unset($this->_struct);   
  return ($tree);   
    }   

    function getChild(&$i) {   
  $children = array();   
  
  while (++$i < $this->_count) {   
      $tagname = $this->_struct[$i]['tag'];   
      $value = isset($this->_struct[$i]['value']) ? $this->_struct[$i]['value'] : '';   
      $attributes = isset($this->_struct[$i]['attributes']) ? $this->_struct[$i]['attributes'] : '';   
  
      switch ($this->_struct[$i]['type']) {   
    case 'open':   
        $child = $this->getChild($i);   
        $children = $this->addNode($children, $tagname, $value, $attributes, $child);   
        break;   
    case 'complete':   
        $children = $this->addNode($children, $tagname, $value, $attributes);   
        break;   
    case 'cdata':   
        $children['value'] .= $value;   
        break;   
    case 'close':   
        return $children;   
        break;   
      }   
     
  }   
    }   
    function addNode($target, $key, $value = '', $attributes = '', $child = '') {   
  if (!isset($target[$key]['xmlvalue']) && !isset($target[$key][0])) {   
      if ($child != '') {   
    $target[$key] = $child;   
      }   
      if ($attributes != '') {   
    foreach ($attributes as $k => $v) {   
        $target[$key][$k] = $v;   
    }   
      }   
         
      $target[$key]['xmlvalue'] = $value;   
  } else {   
      if (!isset($target[$key][0])) {   
    $oldvalue = $target[$key];   
    $target[$key] = array();   
    $target[$key][0] = $oldvalue;   
    $index = 1;   
      } else {   
    $index = count($target[$key]);   
      }   
  
      if ($child != '') {   
    $target[$key][$index] = $child;   
      }   
  
      if ($attributes != '') {   
    foreach ($attributes as $k => $v) {   
        $target[$key][$index][$k] = $v;   
    }   
      }   
      $target[$key][$index]['value'] = $value;   
  }   
  return $target;   
    }   

	}
?>