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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use App\Entity\CommentFile;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



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
        FormFactoryInterface $formFactory,
        Request $request,
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        ObjectManager $manager
        ) {
            $product = $repository->find($product);
            if (!$product) {
                throw new NotFoundHttpException("This Id does not exist");
            }
            
            $comment = new Comment();
            $form = $formFactory->create(
                CommentType::class,
                $comment,
                ['stateless' => true]
                );
            
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tmpCommentFile = [];
                
                foreach ($comment->getFiles() as $fileArray) {
                    foreach ($fileArray as $file) {
                        $name = sprintf(
                            '%s.%s',
                            Uuid::uuid1(),
                            $file->getClientOriginalExtension()
                            );
                        
                        $commentFile = new CommentFile();
                        $commentFile->setComment($comment)
                        ->setMimeType($file->getMimeType())
                        ->setName($file->getClientOriginalName())
                        ->setFileUrl('/upload/'.$name);
                        
                        $tmpCommentFile[] = $commentFile;
                        
                        $file->move(
                            __DIR__.'/../../public/upload',
                            $name
                            );
                            $manager->persist($commentFile);
                    }
                }
                
                $token = $tokenStorage->getToken();
                if (!$token){
                    throw new \Exception();
                }
                $user = $token->getUser();
                if (!$user){
                    throw new \Exception();
                }
                
                $comment->setFiles($tmpCommentFile)
                ->setAuthor($user)
                ->setProduct($product);
                
                $manager->persist($comment);
                $manager->flush();
                
                return new RedirectResponse($urlGenerator->generate('product', ['product' => $product->getId()]));
                
                
            }
        
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



















































