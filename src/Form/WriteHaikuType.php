<?php

namespace App\Form;

use App\Entity\Haikus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class WriteHaikuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line1', TextType::class, [
                'label' => 'Introduction', // Voir pour changement label
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Les 3 champs sont obligatoires'])
                ],
             ])
            ->add('line2', TextType::class, [
                'label' => 'Développement', // Voir pour changement label
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Les 3 champs sont obligatoires'])
                ],
             ])
            ->add('line3', TextType::class, [
                'label' => 'Conclusion', // Voir pour changement label
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Les 3 champs sont obligatoires'])
                ],
             ])
             ->add('submit', SubmitType::class, ['label' => 'Valider']);
        ;
    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Haikus::class,
    //     ]);
    // }
}
