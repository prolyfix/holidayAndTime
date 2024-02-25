<?php

namespace App\Test\Controller;

use App\Entity\Timesheet;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TimesheetControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/timesheet/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Timesheet::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Timesheet index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'timesheet[startTime]' => 'Testing',
            'timesheet[endTime]' => 'Testing',
            'timesheet[break]' => 'Testing',
            'timesheet[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Timesheet();
        $fixture->setStartTime('My Title');
        $fixture->setEndTime('My Title');
        $fixture->setBreak('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Timesheet');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Timesheet();
        $fixture->setStartTime('Value');
        $fixture->setEndTime('Value');
        $fixture->setBreak('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'timesheet[startTime]' => 'Something New',
            'timesheet[endTime]' => 'Something New',
            'timesheet[break]' => 'Something New',
            'timesheet[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/timesheet/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getStartTime());
        self::assertSame('Something New', $fixture[0]->getEndTime());
        self::assertSame('Something New', $fixture[0]->getBreak());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Timesheet();
        $fixture->setStartTime('Value');
        $fixture->setEndTime('Value');
        $fixture->setBreak('Value');
        $fixture->setUser('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/timesheet/');
        self::assertSame(0, $this->repository->count([]));
    }
}
