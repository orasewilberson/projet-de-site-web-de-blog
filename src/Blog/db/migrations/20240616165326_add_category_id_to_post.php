<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCategoryIdToPost extends AbstractMigration
{
   
    public function change(): void
    {
        $this->table('posts')
        ->addColumn('category_id', 'integer', ['signed' => false, 'null' => true])
        ->addIndex(['category_id'])
        ->addForeignKey('category_id', 'categories', 'id', ['delete' => 'SET NULL', 'update' => 'NO_ACTION'])
        ->update();
    }
}
