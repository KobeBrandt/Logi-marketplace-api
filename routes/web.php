<?php

use App\Http\Requests\PluginIndexRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('docs', [
        'apiUrl' => url('/api/v1'),
        'serverCacheMinutes' => intdiv(config('services.logi_marketplace.cache_ttl'), 60),
        'defaultPerPage' => PluginIndexRequest::DEFAULT_PER_PAGE,
        'maxPerPage' => PluginIndexRequest::MAX_PER_PAGE,
    ]);
});

Route::prefix('discord-bot')->name('discord-bot.')->group(function () {
    // A short link that always points at the current Discord invite, so it can be shared anywhere.
    Route::get('/invite', function () {
        $clientId = config('services.discord_bot.client_id');
        abort_if(blank($clientId), 503, 'The bot invite link is not set up yet.');

        return redirect()->away('https://discord.com/oauth2/authorize?'.http_build_query([
            'client_id' => $clientId,
            'permissions' => config('services.discord_bot.permissions'),
            'scope' => 'bot applications.commands',
        ], arg_separator: '&', encoding_type: PHP_QUERY_RFC3986));
    })->name('invite');
});
