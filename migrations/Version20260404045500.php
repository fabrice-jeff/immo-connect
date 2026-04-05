<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260404045500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le champ location a la table property.';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('property');
        if ($table->hasColumn('location')) {
            return;
        }

        $this->addSql("ALTER TABLE property ADD location VARCHAR(255) NOT NULL DEFAULT 'Localisation a completer'");
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('property');
        if (!$table->hasColumn('location')) {
            return;
        }

        $this->addSql('ALTER TABLE property DROP location');
    }
}
