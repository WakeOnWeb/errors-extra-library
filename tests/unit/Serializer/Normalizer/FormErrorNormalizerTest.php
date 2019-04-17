<?php
namespace WakeOnWeb\ErrorsExtraLibrary\Serializer\Normalizer;

use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Tests\Extension\Core\Type\FormTypeTest;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Tests\Fixtures\FakeMetadataFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use WakeOnWeb\ErrorsExtraLibrary\Infra\Serializer\Normalizer\FormErrorNormalizer;

class FormErrorNormalizerTest extends FormTypeTest
{
    private $normalizer;

    public function getExtensions()
    {
        $extensions = parent::getExtensions();
        $metadataFactory = new FakeMetadataFactory();
        $metadataFactory->addMetadata(new ClassMetadata(  Form::class));
        $validator = $this->createValidator($metadataFactory);
        $extensions[] = new CoreExtension();
        $extensions[] = new ValidatorExtension($validator);

        return $extensions;
    }


    protected function createValidator(MetadataFactoryInterface $metadataFactory, array $objectInitializers = array())
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('en');
        $contextFactory = new ExecutionContextFactory($translator);
        $validatorFactory = new ConstraintValidatorFactory();
        return new RecursiveValidator($contextFactory, $metadataFactory, $validatorFactory, $objectInitializers);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->normalizer = new FormErrorNormalizer();
    }

    public function testSupportsNormalization()
    {

        $formInvalid = $this->getForm();
        $formInvalid->submit([]);

        $formValid = $this->getForm();
        $formValid->submit(['task' => 'Task', 'firstName' => 'John', 'lastName' => 'John']);

        $this->assertTrue($this->normalizer->supportsNormalization($formInvalid));
        $this->assertFalse($this->normalizer->supportsNormalization($formValid));
        $this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));

    }

    public function testNormalizeWithChild()
    {
        $formInvalid = $this->getForm();
        $formInvalid->submit(['firstName' => 'LL']);

         $expected = [
             'code' => '400',
             'message' => 'Validation Failed',
             'errors' =>
                 [
                     'children' =>
                         [
                             'firstName' =>
                                 [
                                     'errors' =>
                                         [
                                             0 =>
                                                 [
                                                     0 => 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
                                                     1 =>
                                                         [
                                                             '{{ value }}' => '"LL"',
                                                             '{{ limit }}' => 3,
                                                         ],
                                                 ],
                                         ],
                                 ],
                             'lastName' =>
                                 [
                                     'errors' =>
                                         [
                                             0 =>
                                                 [
                                                     0 => 'This value should not be blank.',
                                                     1 =>
                                                         [
                                                             '{{ value }}' => 'null',
                                                         ],
                                                 ],
                                         ],
                                 ],
                         ],
                 ],
         ];

        $this->assertEquals($expected, $this->normalizer->normalize($formInvalid));
    }


    private function getForm()
    {
        $form = $this->factory->createBuilder(FormType::class);
        $form
            ->add('task', TextType::class)
            ->add('firstName', TextType::class, [
                'constraints' => new Length(['min' => 3]),
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ;

        return $form->getForm();
    }
}