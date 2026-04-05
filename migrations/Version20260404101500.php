<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260404101500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le champ operation a la table property.';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('property');
        if ($table->hasColumn('operation')) {
            return;
        }

        $this->addSql("ALTER TABLE property ADD operation VARCHAR(40) NOT NULL DEFAULT 'A vendre'");
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('property');
        if (!$table->hasColumn('operation')) {
            return;
        }

        $this->addSql('ALTER TABLE property DROP operation');
    }
}
