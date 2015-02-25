<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) zzuutt                                                     */
/*      email : zzuutt34@free.fr                                                       */
/*      web :                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerRef\EventListeners;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Customer\CustomerEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ConfigQuery;
use Thelia\Log\Tlog;

/**
 * Class OrderListener
 * @package CustomerRef\EventListeners
 * @author zzuutt <zzuutt34@free.fr>
 */
class OrderListener implements EventSubscriberInterface
{

    public function implementCustomer(CustomerEvent $event)
    {
        $customer = $event->getCustomer();
        
        
        //if ($order->isPaid() && null === $order->getCustomerRef()) {
        
            //customerRef -> (compteur) valeur correspondant au nombre de client
            $customerRef = ConfigQuery::create()
                ->findOneByName('customerRef');

            if (null === $customerRef) {
                throw new \RuntimeException("you must set an customer ref in your admin panel");
            }
            $value = $customerRef->getValue();
            Tlog::getInstance()->debug("Nombre de client(s) :".$value);
            
            //MaskcustomerRef -> masque pour generer le numero du client
            $MaskcustomerRef = ConfigQuery::create()
                ->findOneByName('MaskcustomerRef');
            
            if (null === $MaskcustomerRef) {
                throw new \RuntimeException("you must set an mask customer ref in your admin panel");
            }
            $Maskvalue = $MaskcustomerRef->getValue();
            Tlog::getInstance()->debug("Masque numero client :".$Maskvalue);

            //MaskComparecustomerRef -> masque de comparaison a la valeur de comparaison pour remettre a zero du compteur
            $MaskComparecustomerRef = ConfigQuery::create()
                ->findOneByName('MaskComparecustomerRef');
            
            if (null === $MaskComparecustomerRef) {
                throw new \RuntimeException("you must set an maskcomparecustomerref in your admin panel");
            }
            $MaskComparevalue = $MaskComparecustomerRef->getValue();            
            Tlog::getInstance()->debug("Masque Compare numero client :".$MaskComparevalue);

            $valeurMaskComparevalue = str_replace('{value}', $value, $MaskComparevalue);
            $valeurMaskComparevalue = preg_replace_callback('/{Date[(](.*?)[)]}/',
                        function ($m) {
                            return Date($m[1]);                    
                        },$valeurMaskComparevalue);
            $valeurMaskComparevalue = preg_replace_callback('/{PadStr\(([a-zA-Z0-9.\/\\-]+),([0-9]+),(.*),(RIGHT|LEFT|BOTH)\)}/', 
                        function ($m) { 
                            if($m[4] == 'LEFT') return str_pad($m[1],$m[2],"$m[3]",STR_PAD_LEFT);
                            if($m[4] == 'RIGHT') return str_pad($m[1],$m[2],"$m[3]",STR_PAD_RIGHT);
                            if($m[4] == 'BOTH') return str_pad($m[1],$m[2],"$m[3]",STR_PAD_BOTH);
                        }, $valeurMaskComparevalue);
            
            //ComparecustomerRef -> valeur de comparaison pour remettre a zero du compteur
            $ComparecustomerRef = ConfigQuery::create()
                ->findOneByName('ComparecustomerRef');
            
            if (null === $ComparecustomerRef) {
                throw new \RuntimeException("you must set an comparecustomerref in your admin panel");
            }
            $Comparevalue = $ComparecustomerRef->getValue();            
            Tlog::getInstance()->debug("Compare numero client :".$Comparevalue);
            
            if($valeurMaskComparevalue <> $Comparevalue) {
              $value=0;
              $ComparecustomerRef->setValue($valeurMaskComparevalue)->save();
            }
            
            $newcustomer = str_replace('{value}', $value, $Maskvalue);
            $newcustomer = preg_replace_callback('/{Date[(](.*?)[)]}/',
                        function ($m) {
                            return Date($m[1]);                    
                        },$newcustomer);
            $newcustomer = preg_replace_callback('/{PadStr\(([a-zA-Z0-9.\/\\-]+),([0-9]+),(.*),(RIGHT|LEFT|BOTH)\)}/', 
                        function ($m) { 
                            if($m[4] == 'LEFT') return str_pad($m[1],$m[2],"$m[3]",STR_PAD_LEFT);
                            if($m[4] == 'RIGHT') return str_pad($m[1],$m[2],"$m[3]",STR_PAD_RIGHT);
                            if($m[4] == 'BOTH') return str_pad($m[1],$m[2],"$m[3]",STR_PAD_BOTH);
                        }, $newcustomer);
            $nouvellecustomerRef = $newcustomer;
            Tlog::getInstance()->debug("Numero de client :".$nouvellecustomerRef);
            //Tlog::getInstance()->debug("Numero de facture :".$value);

            $customer->setref($nouvellecustomerRef)->save();
            $customerRef->setValue(++$value)->save();
        //}
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::AFTER_CREATECUSTOMER => ['implementCustomer', 100]
        ];
    }
}