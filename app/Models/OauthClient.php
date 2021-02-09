<?php

namespace App\Models;

use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;

class OauthClient extends Client
{
    protected $fillable = ['fail_redirect_url', 'webhook_url', 'webhook_secret'];

    public static function findByRequest(?Request $request = null) : ?OAuthClient
    {
        $bearerToken = $request !== null ? $request->bearerToken() : RequestFacade::bearerToken();

        $parsedJwt = (new Parser())->parse($bearerToken);

        if ($parsedJwt->hasHeader('jti')) {
            $tokenId = $parsedJwt->getHeader('jti');
        } elseif ($parsedJwt->hasClaim('jti')) {
            $tokenId = $parsedJwt->getClaim('jti');
        } else {
            Log::error('Invalid JWT token, Unable to find JTI header');
            return null;
        }

        $clientId = Token::find($tokenId)->client->id;

        return (new static)->findOrFail($clientId);
    }

}
