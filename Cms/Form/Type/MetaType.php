<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Cms\Form\Type;

use ChamberOrchestra\MetaBundle\Cms\Form\Dto\MetaDto;
use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use ChamberOrchestra\FileBundle\Cms\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MetaType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MetaDto::class,
            'translation_domain' => 'cms',
            'label_format' => 'meta.field.%name%',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('metaImage', ImageType::class, [
                'required' => false,
                'constraints' => [
                    new Image(),
                ],
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => ['maxlength' => $max = 127],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $max]),
                ],
            ])
            ->add('robotsBehaviour', ChoiceType::class, [
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices' => RobotsBehaviour::choices(),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'required' => false,
                'attr' => ['maxlength' => $max = 127],
                'constraints' => [
                    new Length(['max' => $max]),
                ],
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => false,
                'attr' => ['maxlength' => $max = 255],
                'constraints' => [
                    new Length(['max' => $max]),
                ],
            ])
            ->add('metaKeywords', TextareaType::class, [
                'required' => false,
                'attr' => ['maxlength' => $max = 255],
                'constraints' => [
                    new Length(['max' => $max]),
                ],
            ]);
    }
}
