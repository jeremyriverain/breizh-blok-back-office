<?php 

namespace App\Tests\WebTests;

class BoulderAreaBackOfficeTest extends BackOfficeTestCase {
    public function testListBoulderAreas () {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->assertIndexPageEntityCount(6);
    }

    public function testSearchBoulderAreas () {
        $this->visitBackOffice(
            userEmail: 'contributor@fixture.com',
        );

        $this->searchResults(query: 'Cremiou');

        $this->assertIndexPageEntityCount(1);
    }
}