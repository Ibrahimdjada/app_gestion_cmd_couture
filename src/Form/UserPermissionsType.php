<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class UserPermissionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $permissions = $options['data']->getPermissions() ?? [];

        // Fonction rapide pour ajouter un champ
        $addField = function ($name, $label) use ($builder, $permissions) {
            $builder->add($name, CheckboxType::class, [
                'label' => $label,
                'required' => false,
                'mapped' => false,
                'data' => in_array($name, $permissions),
            ]);
        };

        // --- Utilisateurs / Tailleurs / Clients ---
        $addField('permissions', 'Voir utilisateurs');
        $addField('edit_user', 'Modifier utilisateurs');
        $addField('deactivate_user', 'Désactiver utilisateurs');

        // --- Mesures ---
        $addField('view_mesure', 'Voir les mesures');
        $addField('add_mesure', 'Ajouter mesure');
        $addField('edit_mesure', 'Modifier mesure');
        $addField('delete_mesure', 'Supprimer mesure');

        // --- Commandes ---
        $addField('view_commande', 'Voir commande');
        $addField('add_commande', 'Ajouter commande');
        $addField('edit_commande', 'Modifier commande');
        $addField('delete_commande', 'Supprimer commande');
        $addField('assign_tailleur', 'Affecter tailleur');
        $addField('pay_reliquat', 'Versement reliquat');
        $addField('finish_commande', 'Terminer commande');
        $addField('recuperate_commande', 'Récupérer commande');
        $addField('generate_receipt', 'Faire reçu');
        $addField('view_model', 'Voir modèles');
        $addField('view_tissu', 'Voir tissus');

        // --- RDV ---
        $addField('view_rdv', 'Voir RDV');

        // --- Prêts ---
        $addField('view_pret', 'Voir prêts');
        $addField('add_pret', 'Ajouter prêt');
        $addField('edit_pret', 'Modifier prêt');
        $addField('delete_pret', 'Supprimer prêt');

        // --- Rapports ---
        $addField('report_period', 'Rapport par période');
        $addField('report_tailleur', 'Rapport par tailleur');
        $addField('report_status', 'Rapport par statut');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
