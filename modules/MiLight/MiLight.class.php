<?php
/**
* MiLight 
* @package project
* @author Alex Sokolov <admin@gelezako.com>
* @copyright Alex Sokolov http://www.xa-xa.pp.ua (c)
* @version 0.1 (wizard, 13:01:05 [Jan 25, 2017])
*/
//
//
class MiLight extends module {
/**
* MiLight
*
* Module class constructor
*
* @access private
*/
function MiLight() {
  $this->name="MiLight";
  $this->title="MiLight";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->frmEdit;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}


/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 global $host;
 if(isset($host)) sg('MiLamp1.Host',$host);
 
  global $updatedTime;
 if(isset($updatedTime)) sg('MiLamp1.updatedTime',$updatedTime);
 
  global $level;
 if(isset($level)) sg('MiLamp1.Level',$level);
 
  global $lamptype;
 if(isset($lamptype)) sg('MiLamp1.LampType',$lamptype);
 
  global $mode;
 if(isset($mode)) sg('MiLamp1.Mode',$mode);
 
  global $zone;
 if(isset($zone)) sg('MiLamp1.Zone',$zone);
 
 $this->get_settings($out);

}

function get_settings(&$out)
{
	$out["host"] = gg('MiLamp1.Host');
	$out["updatedTime"] = gg('MiLamp1.updatedTime');
	$out["level"] = gg('MiLamp1.Level');
	$out["lamptype"] = gg('MiLamp1.LampType');
	$out["mode"] = gg('MiLamp1.Mode');
	$out["zone"] = gg('MiLamp1.Zone');
}

/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
 $className = 'MiLight'; //имя класса
 $objectName = array('MiLamp1');//имя обьектов
 $objDescription = array('Лампа1'); //даже описание объектов есть
 $rec = SQLSelectOne("SELECT ID FROM classes WHERE TITLE LIKE '" . DBSafe($className) . "'");
 
    if (!$rec['ID']) {
        $rec = array();
        $rec['TITLE'] = $className;
        $rec['DESCRIPTION'] = 'Модуль для подключения MiLight устройств';
        $rec['ID'] = SQLInsert('classes', $rec);
    }
    for ($i = 0; $i < count($objectName); $i++) {
        $obj_rec = SQLSelectOne("SELECT ID FROM objects WHERE CLASS_ID='" . $rec['ID'] . "' AND TITLE LIKE '" . DBSafe($objectName[$i]) . "'");
        if (!$obj_rec['ID']) {
            $obj_rec = array();
            $obj_rec['CLASS_ID'] = $rec['ID'];
            $obj_rec['TITLE'] = $objectName[$i];
            $obj_rec['DESCRIPTION'] = $objDescription[$i];
            $obj_rec['ID'] = SQLInsert('objects', $obj_rec);
        }
    }

addClassMethod('MiLight', 'disco', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",1);$this->callMethod("sendCommand",array("command"=>"disco"));');
addClassMethod('MiLight', 'discofaster', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",1);$this->callMethod("sendCommand",array("command"=>"discofaster));');
addClassMethod('MiLight', 'discoslower', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",1);$this->callMethod("sendCommand",array("command"=>"discoslower));');
addClassMethod('MiLight', 'refresh', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$status=$this->getProperty("status");if($status){$this->callMethod("turnOn");}else{$this->callMethod("turnOff");');
addClassMethod('MiLight', 'refreshLevel', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$level=$this->getProperty("Level");if($level>0){$this->callMethod("setLevel",array("level"=>$level));}else{$this->callMethod("turnOff");}');
addClassMethod('MiLight', 'sendCommand', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("updated",time());$this->setProperty("updatedTime",date("H:i"));include_once(ROOT."lib/hardware/milight.php");if (is_array($params)){ $command=$params["command"]; $value=$params["value"];}else{$command=$params;}$host=$this->getProperty("Host");$type=(int)$this->getProperty("LampType");$zone=(int)$this->getProperty("Zone");
$milight = new Milight($host);$commands="";if ($type==0){$milight->setWhiteActiveGroup($zone);if ($command=="leveldown"){$milight->command("whiteBrightnessDown");}if($command=="levelup"){$milight->command("whiteBrightnessUp");}if($command=="level" && $value>=90){$command="levelmax";}if($command=="level" && $value<=90){$command="levelmin";}if ($command=="levelmax"){$milight->command("whiteGroup".$zone."BrightnessMax");} 
if ($command=="levelmin") {
  $milight->command("whiteGroup".$zone."BrightnessMin");}  
 if ($command=="nightmode") {
  $milight->command("whiteGroup".$zone."NightMode");}  
 if ($zone==1) {
  if ($command=="on") {
   $milight->whiteGroup1On();}
  if ($command=="off") {
   $milight->whiteGroup1Off(); }}
 if ($zone==2) {
  if ($command=="on") {
   $milight->whiteGroup2On();}
  if ($command=="off") {
   $milight->whiteGroup2Off(); }} 
 if ($zone==3) {
  if ($command=="on") {
   $milight->whiteGroup3On();}
  if ($command=="off") {
   $milight->whiteGroup3Off(); }} 
 if ($zone==4) {
  if ($command=="on") {
   $milight->whiteGroup4On();}
  if ($command=="off") {
   $milight->whiteGroup4Off(); }} }
if ($type==1) {
 if ($command=="disco") {
   $milight->setRgbwActiveGroup($zone);  
   $milight->rgbwSendOnToActiveGroup();
   $milight->command("rgbwDiscoMode");}
 if ($command=="discofaster") {
   $milight->setRgbwActiveGroup($zone);  
   $milight->rgbwSendOnToActiveGroup();
   $milight->command("rgbwDiscoFaster");} 
 if ($command=="discoslower") {
   $milight->setRgbwActiveGroup($zone);  
   $milight->rgbwSendOnToActiveGroup();
   $milight->command("rgbwDiscoSlower");} 
 if ($command=="level") {
  $milight->setRgbwActiveGroup($zone);
  $milight->rgbwBrightnessPercent($value);}
 if ($command=="color") {
  $milight->setRgbwActiveGroup($zone);
  $milight->rgbwSetColorHexString($value);} 
 if ($zone==1) {
  if ($command=="on") {
   $milight->rgbwGroup1On();}
  if ($command=="off") {
   $milight->rgbwGroup1Off();
  }
  if ($command=="white") {
   $milight->rgbwGroup1SetToWhite();}}
 if ($zone==2) {
  if ($command=="on") {
   $milight->rgbwGroup2On();}
  if ($command=="off") {
   $milight->rgbwGroup2Off();}
  if ($command=="white") {
   $milight->rgbwGroup2SetToWhite();}}
 if ($zone==3){
  if ($command=="on"){
   $milight->rgbwGroup3On();}
  if ($command=="off"){
   $milight->rgbwGroup3Off();}
  if ($command=="white"){
   $milight->rgbwGroup3SetToWhite();}} 
 if ($zone==4){
  if ($command=="on"){
   $milight->rgbwGroup4On(); }
  if ($command=="off"){
   $milight->rgbwGroup4Off();}
  if ($command=="white"){
   $milight->rgbwGroup4SetToWhite();}}}
sleep(1);');
addClassMethod('MiLight', 'setColor', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",1);$this->setProperty("Mode","C");if ($params["color"]){$this->setProperty("Color",$params["color"]);}else{$params["color"]=$this->getProperty("Color");}$this->callMethod("sendCommand",array("command"=>"color","value"=>$params["color"]));','test');
addClassMethod('MiLight', 'setLevel', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");if($params["level"]>0){ $this->setProperty("status",1);} else{$this->setProperty("status",0);}$this->setProperty("Level",$params["level"]);$this->callMethod("sendCommand",array("command"=>"level","value"=>$params["level"]));');
addClassMethod('MiLight', 'setRandomColor', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");/*$colors=array("#ff0000","#00ff00","#0000ff");$color=$colors[rand(0,count($colors)-1)];*/$rand=array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");$color="#".$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];$this->callMethod("setColor",array("color"=>$color));');
addClassMethod('MiLight', 'setWhite', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",1);$this->setProperty("Mode","W");$this->callMethod("sendCommand",array("command"=>"white"));');
addClassMethod('MiLight', 'turnOff', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",0);$this->callMethod("sendCommand",array("command"=>"off"));');
addClassMethod('MiLight', 'turnOn', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");$this->setProperty("status",1);$this->callMethod("sendCommand",array("command"=>"on"));');

addClassProperty('MiLight', 'Color', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'Host', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'LampType', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'Level', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'LinkedRoom', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'Mode', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'status', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'updated', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'updatedTime', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');
addClassProperty('MiLight', 'Zone', 'include_once(DIR_MODULES."MiLight/MiLight.class.php");');

  parent::install();
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDI1LCAyMDE3IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
