<?php
namespace tests\Table;

use PDO;
use App\Blog\Entity\Post;
use tests\DatabaseTestCase;
use App\Blog\Table\PostTable;
use Framework\Database\NoRecordException;


class PostTableTest extends DatabaseTestCase {
    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp(): void {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind() 
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord() 
    {
        $this->expectException(NoRecordException::class);
        $this->postTable->find(1);
        
    }

    public function testUpdate() 
    {
      $this->seedDatabase($this->postTable->getPdo());
      $this->postTable->update(1, ['name' => 'salut', 'slug' => 'demo']);
      $post = $this->postTable->find(1);
      $this->assertEquals('salut', $post->name);
      $this->assertEquals('demo', $post->slug);  
    }

    public function testInsert()
    {
        $this->postTable->insert(['name' => 'salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('salut', $post->name);
        $this->assertEquals('demo', $post->slug);  
      }

      public function testDelete()
    {
        $this->postTable->insert(['name' => 'salut', 'slug' => 'demo']);
        $this->postTable->insert(['name' => 'salut', 'slug' => 'demo']);
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
    
}