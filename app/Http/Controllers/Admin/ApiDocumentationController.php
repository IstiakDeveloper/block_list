<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MisLoanIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApiDocumentationController extends Controller
{
    public function __construct(
        private readonly MisLoanIntegrationService $integration,
    ) {}

    public function index(Request $request): Response
    {
        $apiBaseUrl = rtrim($request->getSchemeAndHttpHost(), '/').'/api';
        $integrationUser = $this->integration->integrationUser();
        $integrationToken = $this->integration->activeToken();

        return Inertia::render('Admin/ApiDocumentation/Index', [
            'apiBaseUrl' => $apiBaseUrl,
            'misloanAppUrl' => rtrim((string) config('services.misloan.app_url'), '/'),
            'integration' => [
                'username' => $this->integration->integrationUsername(),
                'user_found' => $integrationUser !== null,
                'user_name' => $integrationUser?->name,
            ],
            'misloan' => [
                'env_url_key' => 'BLOCK_LIST_API_URL',
                'env_token_key' => 'BLOCK_LIST_API_TOKEN',
                'env_url_value' => $apiBaseUrl,
                'token_active' => $integrationToken !== null,
                'token_created_at' => $integrationToken?->created_at?->toDateTimeString(),
                'token_last_used_at' => $integrationToken?->last_used_at?->toDateTimeString(),
                'plain_token' => $request->session()->get('misloan_plain_token'),
            ],
        ]);
    }

    public function generateToken(Request $request): RedirectResponse
    {
        try {
            $result = $this->integration->generateToken();
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.api-documentation.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.api-documentation.index')
            ->with('success', 'MisLoan API token তৈরি হয়েছে (user: '.$result['username'].')। নিচে কপি করে MisLoan .env-এ বসান।')
            ->with('misloan_plain_token', $result['plain_token']);
    }

    public function revokeToken(): RedirectResponse
    {
        $this->integration->revokeToken();

        return redirect()
            ->route('admin.api-documentation.index')
            ->with('success', 'MisLoan API token বাতিল করা হয়েছে।')
            ->forget('misloan_plain_token');
    }

    public function testConnection(Request $request): RedirectResponse
    {
        $apiBaseUrl = rtrim($request->getSchemeAndHttpHost(), '/').'/api';
        $result = $this->integration->testApiReachable($apiBaseUrl);

        return redirect()
            ->route('admin.api-documentation.index')
            ->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function clearDisplayedToken(): RedirectResponse
    {
        return redirect()
            ->route('admin.api-documentation.index')
            ->forget('misloan_plain_token');
    }
}
