<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Gateway.php
 * @author     Minh Nguyen
 */
class Sitebooking_Api_Gateway extends Core_Api_Abstract
{
     /**
     * Save setting gateway. Update info if it exists
     * 
     * @param mixed $gateway_name
     * @param mixed $params
     */
     public function saveSettingGateway($gateway_name ='paypal',$params = array()) 
     {
         $gateway = Sitebooking_Api_Gateway::getSettingGateway($gateway_name);
         
         if ( $gateway != null)
         {
             
            $table  = Engine_Api::_()->getDbtable('gateways', 'sitebooking');
            $where = $table->getAdapter()->quoteInto('gateway_name = ?',$gateway_name);
            $table->update($gateway, $where); 
         }
         else
         {
           
             $params['gateway_name'] = $gateway_name;
             $params['params'] = serialize($params['params']);
             $table  = Engine_Api::_()->getDbtable('gateways', 'sitebooking');
             $t = $table->createRow();
             $t->gateway_name = $params['gateway_name'];
             $t->save();
         }
             
     }
     /**
     * Get setting of gateway 
     * 
     * @param mixed $gateway_name
     */
     public function getSettingGateway($gateway_name = 'paypal')   
     {
          $l_table  = Engine_Api::_()->getDbTable('gateways', 'sitebooking'); 
          $select   = $l_table->select()
                       ->from($l_table)->where('gateway_name = ?',$gateway_name);
         $result =  $l_table->fetchAll($select)->toArray();
         return @$result[0];
     }
}   
?>
