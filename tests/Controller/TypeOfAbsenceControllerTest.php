<?php

namespace App\Test\Controller;

use App\Entity\TypeOfAbsence;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TypeOfAbsenceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/type/of/absence/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(TypeOfAbsence::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('TypeOfAbsence index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'type_of_absence[name]' => 'Testing',
            'type_of_absence[creationDate]' => 'Testing',
            'type_of_absence[isHoliday]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new TypeOfAbsence();
        $fixture->setName('My Title');
        $fixture->setCreationDate('My Title');
        $fixture->setIsHoliday('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('TypeOfAbsence');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new TypeOfAbsence();
        $fixture->setName('Value');
        $fixture->setCreationDate('Value');
        $fixture->setIsHoliday('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'type_of_absence[name]' => 'Something New',
            'type_of_absence[creationDate]' => 'Something New',
            'type_of_absence[isHoliday]' => 'Something New',
        ]);

        self::assertResponseRedirects('/type/of/absence/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getCreationDate());
        self::assertSame('Something New', $fixture[0]->getIsHoliday());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new TypeOfAbsence();
        $fixture->setName('Value');
        $fixture->setCreationDate('Value');
        $fixture->setIsHoliday('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/type/of/absence/');
        self::assertSame(0, $this->repository->count([]));
    }
}
