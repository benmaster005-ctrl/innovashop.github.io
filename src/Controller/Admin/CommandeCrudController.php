<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use DatePeriod;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            IdField::new('utilisateur.id'),    
            TextField::new('utilisateur.adress', 'Adresse du client')
                ->renderAsHtml()
                ->formatValue(function ($value, $entity) {
                    return $entity->getUtilisateur() ? $entity->getUtilisateur()->getAdress() : 'N/A';
                })
                ->hideOnForm(),
            TextField::new('status'),
            DateTimeField::new('date'),
            MoneyField::new('total')
                ->setCurrency('EUR')
            
            
        ];
    }
    
}
