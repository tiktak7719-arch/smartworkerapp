<?php

namespace App\Http\Controllers;

use App\Models\SmartNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(25);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(SmartNotification $notification, Request $request): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);

        return redirect($this->safeUrl($notification->url, $request));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    private function safeUrl(?string $url, Request $request): string
    {
        if (! $url) {
            return route('notifications.index');
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $allowedHosts = array_filter([
            parse_url(config('app.url'), PHP_URL_HOST),
            $request->getHost(),
        ]);

        return $host && in_array($host, $allowedHosts, true)
            ? $url
            : route('notifications.index');
    }
}
