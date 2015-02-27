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

namespace CustomerRef;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Module\BaseModule;
use CustomerRef\Model\Config;

class CustomerRef extends BaseModule
{
    const DOMAIN_NAME = "customerref";
    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */
     
    public function preActivation(ConnectionInterface $con = null)
    {
        /* THELIA VERSION */
        $thelia_major_version = ConfigQuery::read('thelia_major_version');
        $thelia_minus_version = ConfigQuery::read('thelia_minus_version');

        /* Check THELIA VERSION */
        if ($thelia_major_version == 2 && $thelia_minus_version < 1) {
            return true;
        }

        return false;
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        ConfigQuery::write('customerRef', 0, true, true);
        ConfigQuery::write('MaskcustomerRef', "{Date(ym)}{PadStr({value},3,0,LEFT)}", true, true);
        ConfigQuery::write('ComparecustomerRef', Date('m'), true, true);
        ConfigQuery::write('MaskComparecustomerRef', "{Date(m)}", true, true);
    }

}
