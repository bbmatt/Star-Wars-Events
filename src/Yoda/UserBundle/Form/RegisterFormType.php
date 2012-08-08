<?php

namespace Yoda\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password'
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Yoda\UserBundle\Entity\User',
        );
    }


    public function getName()
    {
        return 'user_register';
    }
}