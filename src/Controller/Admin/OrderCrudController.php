<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
//use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class OrderCrudController extends AbstractCrudController {

    public static function getEntityFqcn(): string {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable {
        return [
            //IdField::new('id'),
            TextField::new('orderId'),
            TextField::new('name'),
            NumberField::new('price'),
            AssociationField::new('status')
        ];
    }

}
