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

namespace CustomerRef\Controller;
use CustomerRef\Form\ConfigurationForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;


/**
 * Class ConfigurationController
 * @package CustomerRef\Controller
 * @author zzuutt <zzuutt34@free.fr>
 */
class ConfigurationController extends BaseAdminController
{

    public function configureAction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'customerref', AccessManager::UPDATE)) {
            return $response;
        }

        $form = new ConfigurationForm($this->getRequest());
        $response = null;
        $error_msg = null;
        try {
            $configForm = $this->validateForm($form);

            ConfigQuery::write('customerRef', $configForm->get('customer')->getData(), true, true);
            ConfigQuery::write('MaskcustomerRef', $configForm->get('Maskcustomer')->getData(), true, true);
            ConfigQuery::write('ComparecustomerRef', $configForm->get('Comparecustomer')->getData(), true, true);
            ConfigQuery::write('MaskComparecustomerRef', $configForm->get('Maskcomparecustomer')->getData(), true, true);
            // Redirect to the success URL,
            if ($this->getRequest()->get('save_mode') == 'stay') {
                // If we have to stay on the same page, redisplay the configuration page/
                $route = '/admin/module/CustomerRef';
            } else {
                // If we have to close the page, go back to the module back-office page.
                $route = '/admin/modules';
            }
            $response = RedirectResponse::create(URL::getInstance()->absoluteUrl($route));
        } catch (FormValidationException $e) {
            $error_msg = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
        }
        if (null !== $error_msg) {
            $this->setupFormErrorContext(
                'CustomerRef Configuration',
                $error_msg,
                $form,
                $e
            );
            $response = $this->render(
                'module-configure',
                ['module_code' => 'CustomerRef']
            );
        }
        return $response;
    }
} 