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

namespace CustomerRef\Form;
use CustomerRef\CustomerRef;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;


/**
 * Class ConfigurationForm
 * @package CustomerRef\Form
 * @author zzuutt <zzuutt34@free.fr>
 */
class ConfigurationForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('customer', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('init Compteur client', [], CustomerRef::DOMAIN_NAME),
                'label_attr' => [
                    'for' => 'customer-ref'
                ],
                'data' => ConfigQuery::read('customerRef', 0)
            ])
            ->add('Maskcustomer', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Masque customer ref', [], CustomerRef::DOMAIN_NAME),
                'label_attr' => [
                    'for' => 'mask-customer-ref'
                ],
                'data' => ConfigQuery::read('MaskcustomerRef', 0)
            ])
            ->add('Comparecustomer', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Valeur de comparaison', [], CustomerRef::DOMAIN_NAME),
                'label_attr' => [
                    'for' => 'compare-customer-ref'
                ],
                'data' => ConfigQuery::read('ComparecustomerRef', 0)
            ])
            ->add('Maskcomparecustomer', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => Translator::getInstance()->trans('Masque de comparaison à la valeur de comparaison', [], CustomerRef::DOMAIN_NAME),
                'label_attr' => [
                    'for' => 'mask-compare-customer-ref'
                ],
                'data' => ConfigQuery::read('MaskComparecustomerRef', 0)
            ])
            ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'customerref_config';
    }
}