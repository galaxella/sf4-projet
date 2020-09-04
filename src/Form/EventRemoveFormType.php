<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EventRemoveFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirmation', CheckboxType::class, [
                'constraints' => [
                    new IsTrue(['message' => 'Veuillez cocher la case pour confirmer la suppression.'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Pas de classe car on ne modifie pas d'objet, on se sert du formulaire uniquement pour confirmation
        ]);
    }
}
