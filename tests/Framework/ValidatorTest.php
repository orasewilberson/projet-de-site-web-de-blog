<?php
namespace tests\Framework;

use PDO;
use Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase{
 
    public function makeValidator(array $params)
    {
        return new Validator($params);
    }

    public function getPdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function testRequiredIfFail() {
        $errors = $this->makeValidator(['name' => 'joe'])
        ->required('name', 'content')
        ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testNotEmpty() {
        $errors = $this->makeValidator(['name' => 'joe', 'content' => ''])
            ->notEmpty('content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testRequiredIfSuccess() {
        $errors = $this->makeValidator(['name' => 'joe', 'content' => 'aze'])
        ->required('name', 'content')
        ->getErrors();
        $this->assertCount(0, $errors);

    }

    public function testSlugSuccess(){
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-azeaze34',
            'slug2' => 'azeaze'
            ])
            ->slug('slug')
            ->slug('slug2')
            ->getErrors();
            $this->assertCount(0, $errors);
    }

    public function testSlugError(){
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-azeAze34',
            'slug2' => 'aze-aze_azeaze34',
            'slug4' => 'aze-azeaze-',
            'slug3' => 'aze--aze-azeaze34'
            ])
            ->slug('slug')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
            $this->assertEquals(['slug', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testlength(){
        $params =['slug' => '123456789'];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        // $this->assertEquals('Le champs slug doit contenir plus de 12 caracteres', (string)$errors['slug']);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());

    }

    public function testDate() {
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->dateTime('date')->getErrors());

    }

    public function testExists() 
    {
         $pdo = $this->getPdo();
        $pdo->exec('CREATE TABLE test(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )');
    
    $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
    $pdo->exec('INSERT INTO test (name) VALUES ("a2")');
    $this->assertTrue($this->makeValidator(['category' => '1'])->exists('category', 'test', $pdo)->isValid());
    $this->assertFalse($this->makeValidator(['category' => '15558'])->exists('category', 'test', $pdo)->isValid());
    }

    public function testUnique() 
    {
         $pdo = $this->getPdo();
        $pdo->exec('CREATE TABLE test(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )');
    
    $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
    $pdo->exec('INSERT INTO test (name) VALUES ("a2")');
    $this->assertFalse($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo)->isValid());

    $this->assertTrue($this->makeValidator(['name' => 'a1111'])->unique('name', 'test', $pdo)->isValid());
    $this->assertTrue($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo, 1)->isValid());

    $this->assertFalse($this->makeValidator(['name' => 'a2'])->unique('name', 'test', $pdo, 1)->isValid());


    }

}