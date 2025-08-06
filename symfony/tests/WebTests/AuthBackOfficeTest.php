<?php

namespace App\Tests\WebTests;

use PHPUnit\Framework\Attributes\DataProvider;

class AuthBackOfficeTest extends BackOfficeTestCase
{
    public function testUserIsRedirectedToLoginPageIfNotAuthenticated()
    {
        $this->client->request('GET', '/admin/fr');
        $this->assertResponseRedirects(
            expectedLocation: '/admin/login/fr',
            expectedCode: 302
        );
    }

    #[DataProvider('authorizedEmails')]
    public function testAuthorizedUserCanAccessBackOffice(string $emailUser)
    {
        $this->client->disableReboot();
        $this->client->request('GET', '/admin/login/fr');

        $this->client->submitForm('Envoyer lien', [
            'email' => $emailUser,
        ]);

        $this->assertEmailCount(1);

        /**
         * @var \Symfony\Bridge\Twig\Mime\NotificationEmail $email
         */
        $email = $this->getMailerMessage();

        $this->assertEquals($emailUser, $email->getTo()[0]->getAddress());
        $this->assertCount(1, $email->getTo());
        $this->assertStringContainsString('/admin/login_check/fr', $email->getContext()['action_url']);
        $this->client->followRedirects();
        $this->client->request('GET', $email->getContext()['action_url']);
        $this->assertSelectorTextContains('h1', 'Secteurs');
    }

    public function testUserCanAuthAsEnglishSpeaker()
    {
        $this->client->disableReboot();
        $this->client->request('GET', '/admin/login/en');

        $this->client->submitForm('Send link', [
            'email' => 'admin@fixture.com',
        ]);

        /**
         * @var \Symfony\Bridge\Twig\Mime\NotificationEmail $email
         */
        $email = $this->getMailerMessage();

        $this->assertStringContainsString('/admin/login_check/en', $email->getContext()['action_url']);
        $this->client->followRedirects();
        $this->client->request('GET', $email->getContext()['action_url']);
        $this->assertSelectorTextContains('h1', 'Boulder areas');
    }

    public static function authorizedEmails(): array
    {
        return [
            ['contributor@fixture.com'],
            ['admin@fixture.com'],
            ['super-admin@fixture.com'],
        ];
    }

    public function testUserCannotAccessBackOffice()
    {
        $this->client->disableReboot();
        $this->client->request('GET', '/admin/login/fr');

        $this->client->submitForm('Envoyer lien', [
            'email' => 'user@fixture.com',
        ]);

        /**
         * @var \Symfony\Bridge\Twig\Mime\NotificationEmail $email
         */
        $email = $this->getMailerMessage();

        $this->assertEquals('user@fixture.com', $email->getTo()[0]->getAddress());
        $this->client->followRedirects();
        $this->client->request('GET', $email->getContext()['action_url']);
        $this->assertResponseStatusCodeSame(403);
    }

    #[DataProvider('logoutParams')]
    public function testLogout(string $locale, string $expectedText)
    {
        $this->client->disableReboot();
        $this->visitBackOffice(userEmail: 'contributor@fixture.com');
        $this->client->followRedirects();
        $this->client->request('GET', "/admin/logout/$locale");
        $this->assertSelectorTextContains('button[type=submit]', $expectedText);
    }

    public static function logoutParams(): array
    {
        return [
            ['fr', 'Envoyer lien'],
            ['en', 'Send link'],
        ];
    }
}
