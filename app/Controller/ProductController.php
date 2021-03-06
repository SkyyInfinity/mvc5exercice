<?php
namespace App\Controller;

use App\Service\Form;
use App\Service\Validation;
use App\Weblitzer\Controller;
use App\Model\ProductModel;

class ProductController extends Controller {
  //METHODE READ
  public function read() {
    $products = ProductModel::all();
    $count = ProductModel::count();
    $this->render('app.product.read',array(
      'products' => $products,
      'count' => $count
    ));
  }

  //METHODE ADD
  public function add() {
    $errors = array();
    if(!empty($_POST['submitted'])) {
      $post = $this->cleanXss($_POST);
      $validation = new Validation();
      $errors = $this->validationProduct($validation, $errors, $post);
      if($validation->IsValid($errors)) {
        ProductModel::insert($post);
        $this->redirect('readproduct');
      }
    }
    $form = new Form($errors);
    $this->render('app.product.add', array(
      'form' => $form
    ));
  }

  //METHODE DETAIL
  public function detail($id) {
    $product = $this->ifProductExistOr404($id);
    $this->render('app.product.detail', array(
      'product' => $product
    ));
  }

  //METHODE UPDATE
  public function update($id) {
    $errors = array();
    $product = $this->ifProductExistOr404($id);
    if(!empty($_POST['submitted'])) {
      $post = $this->cleanXss($_POST);
      $validation = new Validation();
      $errors = $this->validationProduct($validation, $errors, $post);
      if($validation->IsValid($errors)) {
        ProductModel::update($id, $post);
        $this->redirect('readproduct');
      }
    }
    $form = new Form($errors);
    $this->render('app.product.update', array(
      'form' => $form,
      'product' => $product
    ));
  }

  //METHODE DELETE
  public function delete($id){
    $product = $this->ifProductExistOr404($id);
    ProductModel::delete($id);
    $this->redirect('readproduct');
  }

  //METHODE REDIRECTION 404
  private function ifProductExistOr404($id)
    {
        $product = ProductModel::findById($id);
        if(empty($product)) {
            $this->Abort404();
        }
        return $product;

    }

    //METHODE VALIDATION
    private function validationProduct($validation,$errors,$post)
    {
      $errors['titre'] = $validation->textValid($post['titre'], 'titre', 3, 255);
      $errors['reference'] = $validation->textValid($post['reference'], 'reference', 3, 255);
      $errors['description'] = $validation->textValid($post['description'], 'description', 10, 2000);
      return $errors;
    }
}
