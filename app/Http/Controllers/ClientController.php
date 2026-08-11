<?php

namespace App\Http\Controllers;

use App\Models\OauthClient;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Rules\RedirectRule;
use Laravel\Passport\Passport;

/**
 * Merchant OAuth client management (Passport).
 *
 * PR-08: Updated for Passport 13 ClientRepository API while preserving
 * existing route names, request fields, and OauthClient webhook extensions.
 * Existing oauth_* tables are kept (Passport 13 is backward-compatible).
 */
class ClientController
{
    /**
     * The client repository instance.
     *
     * @var \Laravel\Passport\ClientRepository
     */
    protected $clients;

    /**
     * The validation factory implementation.
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validation;

    /**
     * The redirect validation rule.
     *
     * @var \Laravel\Passport\Http\Rules\RedirectRule
     */
    protected $redirectRule;

    /**
     * Create a client controller instance.
     *
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @param  \Illuminate\Contracts\Validation\Factory  $validation
     * @param  \Laravel\Passport\Http\Rules\RedirectRule  $redirectRule
     * @return void
     */
    public function __construct(
        ClientRepository $clients,
        ValidationFactory $validation,
        RedirectRule $redirectRule
    ) {
        $this->clients = $clients;
        $this->validation = $validation;
        $this->redirectRule = $redirectRule;
    }

    /**
     * Get all of the clients for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser(Request $request)
    {
        $clients = $this->clients->forUser($request->user());

        if (Passport::$hashesClientSecrets) {
            return $clients;
        }

        return $clients->makeVisible('secret');
    }

    /**
     * Store a new client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Passport\Client|array
     */
    public function store(Request $request)
    {
        $this->validation->make($request->all(), [
            'name' => 'required|max:50',
            'redirect' => ['required', $this->redirectRule],
            'fail_redirect_url' => ['nullable', 'url'],
            'webhook_url' => ['nullable', 'url'],
            'confidential' => 'boolean',
        ])->validate();

        $redirectUris = array_values(array_filter(array_map('trim', explode(',', $request->redirect))));

        $client = $this->clients->createAuthorizationCodeGrantClient(
            $request->name,
            $redirectUris,
            (bool) $request->input('confidential', true),
            $request->user()
        );

        if (Passport::$hashesClientSecrets) {
            return ['plainSecret' => $client->plainSecret] + $client->toArray();
        }

        $oauth_client = OauthClient::find($client->id);
        $oauth_client->fail_redirect_url = $request->fail_redirect_url;
        $oauth_client->webhook_secret = '';
        if ($request->webhook_url) {
            $oauth_client->webhook_url = $request->webhook_url;
            $oauth_client->webhook_secret = Str::random(20);
        }
        $oauth_client->save();

        return $client->makeVisible('secret');
    }

    /**
     * Update the given client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client|bool
     */
    public function update(Request $request, $clientId)
    {
        $client = $this->clients->findForUser($clientId, $request->user());

        if (! $client) {
            return new Response('', 404);
        }

        $this->validation->make($request->all(), [
            'name' => 'required|max:50',
            'redirect' => ['required', $this->redirectRule],
        ])->validate();

        $oauth_client = OauthClient::find($client->id);
        $oauth_client->fail_redirect_url = $request->fail_redirect_url;
        $oauth_client->webhook_secret = '';
        if ($request->webhook_url) {
            $oauth_client->webhook_url = $request->webhook_url;
            $oauth_client->webhook_secret = Str::random(20);
        }
        $oauth_client->save();

        $redirectUris = array_values(array_filter(array_map('trim', explode(',', $request->redirect))));

        $this->clients->update($client, $request->name, $redirectUris);

        return $client->fresh()->makeVisible('secret');
    }

    /**
     * Delete the given client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $clientId)
    {
        $client = $this->clients->findForUser($clientId, $request->user());

        if (! $client) {
            return new Response('', 404);
        }

        $this->clients->delete($client);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
