<?php

namespace App\Form;

use App\Entity\Note;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content'
                , null, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a note',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Your note should be at least {{ limit }} characters',
                        'max' => 4096,
                    ])],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
