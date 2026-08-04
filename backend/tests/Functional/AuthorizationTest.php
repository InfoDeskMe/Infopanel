<?php

namespace App\Tests\Functional;

use App\Security\Roles;
use App\Tests\DatabaseTestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * The most important test file in the project (see SEC-1): every endpoint
 * that's supposed to be admin-only must actually reject an editor with 403,
 * not just hide the link in the UI.
 */
class AuthorizationTest extends WebTestCase
{
    use DatabaseTestTrait;

    private KernelBrowser $client;
    private $admin;
    private $editor;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->setUpDatabase();

        $this->admin = $this->createUser('admin@test.local', [Roles::ROLE_ADMIN], 'adminpass123');
        $this->editor = $this->createUser('editor@test.local', [Roles::ROLE_EDITOR], 'editorpass123');
    }

    /**
     * Reads the CSRF token off the page's own meta tag rather than pulling
     * one from the container directly -- it has to come from the same
     * session the subsequent request's cookie carries, exactly like the
     * real frontend JS does (see api.js).
     */
    private function fetchApiCsrfToken(): string
    {
        $crawler = $this->client->request('GET', '/gui');
        $token = $crawler->filter('meta[name="csrf-token"]')->attr('content');
        $this->assertNotNull($token, 'expected a csrf-token meta tag on /gui');

        return $token;
    }

    public function testAnonymousIsRedirectedToLogin(): void
    {
        $this->client->request('GET', '/api/show_users');

        $this->assertResponseRedirects('/login');
    }

    public function testEditorIsDeniedAdminOnlyEndpoints(): void
    {
        $this->client->loginUser($this->editor);
        $csrfToken = $this->fetchApiCsrfToken();

        $this->client->request('GET', '/api/show_users');
        $this->assertResponseStatusCodeSame(403, 'editor should not see the user list');

        $this->client->request('GET', '/gui_users');
        $this->assertResponseStatusCodeSame(403, 'editor should not reach the users page');

        $this->client->request('GET', '/gui_settings');
        $this->assertResponseStatusCodeSame(403, 'editor should not reach the settings page');

        $this->client->request(
            'POST',
            '/api/create_user',
            [],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken],
            json_encode(['user' => 'new@test.local', 'password' => 'somepassword1', 'role' => Roles::ROLE_EDITOR])
        );
        $this->assertResponseStatusCodeSame(403, 'editor should not be able to create users');

        $this->client->request(
            'POST',
            '/api/delete_user',
            [],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken],
            json_encode(['userToDelete' => 'admin@test.local'])
        );
        $this->assertResponseStatusCodeSame(403, 'editor should not be able to delete users');

        $this->client->request(
            'POST',
            '/api/save_settings',
            [
                'basicReload' => 60000, 'newsNumber' => 4, 'tickerSpeed' => 10, 'layoutOrientation' => 0,
                'tickerOrientation' => 'left', 'tickerHeight' => 80, 'showTicker' => 1, 'slideDelay' => 8000,
            ],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken]
        );
        $this->assertResponseStatusCodeSame(403, 'editor should not be able to save settings');
    }

    public function testAdminCanAccessAdminOnlyEndpoints(): void
    {
        $this->client->loginUser($this->admin);
        $csrfToken = $this->fetchApiCsrfToken();

        $this->client->request('GET', '/api/show_users');
        $this->assertResponseIsSuccessful('admin should see the user list');

        $this->client->request('GET', '/gui_users');
        $this->assertResponseIsSuccessful('admin should reach the users page');

        $this->client->request('GET', '/gui_settings');
        $this->assertResponseIsSuccessful('admin should reach the settings page');

        $this->client->request(
            'POST',
            '/api/save_settings',
            [
                'basicReload' => 60000, 'newsNumber' => 4, 'tickerSpeed' => 10, 'layoutOrientation' => 0,
                'tickerOrientation' => 'left', 'tickerHeight' => 80, 'showTicker' => 1, 'slideDelay' => 8000,
            ],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken]
        );
        $this->assertResponseIsSuccessful('admin should be able to save settings');
    }

    public function testEditorCanChangeOwnPasswordButNotSomeoneElses(): void
    {
        $this->client->loginUser($this->editor);
        $csrfToken = $this->fetchApiCsrfToken();

        $this->client->request(
            'POST',
            '/api/change_password',
            [],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken],
            json_encode(['users' => 'admin@test.local', 'newPass' => 'newpassword123'])
        );
        $this->assertResponseStatusCodeSame(403, 'editor should not be able to change the admin\'s password');

        $this->client->request(
            'POST',
            '/api/change_password',
            [],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken],
            json_encode(['users' => 'editor@test.local', 'newPass' => 'newpassword123'])
        );
        $this->assertResponseIsSuccessful('editor should be able to change their own password');
    }

    public function testAdminCannotDeleteTheLastRemainingAdmin(): void
    {
        $this->client->loginUser($this->admin);
        $csrfToken = $this->fetchApiCsrfToken();

        $this->client->request(
            'POST',
            '/api/delete_user',
            [],
            [],
            ['HTTP_X-CSRF-Token' => $csrfToken],
            json_encode(['userToDelete' => 'admin@test.local'])
        );
        $this->assertResponseStatusCodeSame(400, 'the last remaining admin must not be deletable');
    }

    public function testEditorCanReachTheNewsPage(): void
    {
        $this->client->loginUser($this->editor);

        $this->client->request('GET', '/gui');
        $this->assertResponseIsSuccessful('editor should be able to reach the news backoffice page');
    }
}
