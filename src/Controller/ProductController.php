<?php
namespace App\Controller;
use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;



class ProductController
{
    public function addProduct(Environment $twig, 
        FormFactoryInterface $factory, 
        Request $request, 
        ObjectMAnager $manager,
        SessionInterface $session
        ){
        
        $product = new Product();
        $builder = $factory->createBuilder(FormType::class, $product);
        $builder->add(
                    'name', TextType::class,
                    [
                        'label'=>'FORM.PRODUCT.NAME'
                    ]    
                    )
                ->add('description',
                    TextareaType::class,
                    [
                        'label'=>'FORM.PRODUCT.DESCRIPTION',
                        'attr'=>[
                            'placeholder'=>'FORM.PRODUCT.PLACEHOLDER.DESCRIPTION'
                        ]
                    ]
                    )
                ->add('version', TextType::class,
                    [
                        'label'=>'FORM.PRODUCT.VERSION'
                    ]  
                    )
                    ->add('submit', SubmitType::class,
                        [
                            'label'=>'FORM.PRODUCT.SUBMIT'
                        ] );
        
                $form = $builder->getForm();
        
                
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($product);
            $manager->flush();
            
            $session->getFlashBag()->add('info', 'Your product was created');
            
            return new RedirectResponse('/');
        }
        //IF the form is submitted
           // then, if the form is valid
              //the, var_dump the data
                
                
        return new Response(
            $twig->render(
                'Product/addProduct.html.twig',
                [
                    'formular'=>$form->createView(),
                    'isTrue=> false'
                ]
            )
        );
    } 
    
    
    public function displayProducts(
            Environment $twig,
            ProjectRepository $productRepository
        ){

        $products= $productRepository->findAll();
        
        return new Response
        (
            $twig->render('Product/displayProducts.html.twig',
                [
                    'products'=>$products
                ]
            )
        );
    }

    public function displayProduct(
            Environment $twig, 
            ProjectRepository $repository, 
            int $product,
            FormFactoryInterface $formFactory
        )
    {
        $product = $repository->find($product);
        if (!$product) {
            throw new NotFoundHttpException();
        }
        
        $comment = new Comment();
        $form = $formFactory->create(
                CommentType::class,
                $comment,
                ['stateless'=>true]
            );
        
        return new Response(
            $twig->render(
                'Product/productDetails.html.twig',
                [
                    'product' => $product,
                    'routeAttr' => ['product' => $product->getId()],
                    'form'=>$form->createView()
                ]
                )
            );
    }
    
    
    
}



















































