<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TeamMemberPermission;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property \App\Models\User $user
 */
final class ExportModal extends Component
{
    use HasModal;
    use InteractsWithUser;
    use AuthorizesRequests;

    /** @var mixed */
    protected $listeners = [
        'triggerExportModal' => 'openModal',
    ];

    /**
     * Start the export for the user.
     *
     * @return StreamedResponse
     */
    final public function export() : StreamedResponse
    {
        $this->authorize(TeamMemberPermission::SERVER_CONFIGURATION_EXPORT);

        $data = $this->generateExportData();

        $this->dispatchBrowserEvent('export-ready');

        return response()->streamDownload(function () use ($data) : void {
            echo json_encode($data);
        }, $this->filename());
    }

    final public function render() : View
    {
        return view('livewire.export-modal');
    }

    /**
     * Generate the entire export data for the download request.
     *
     * @return array
     */
    private function generateExportData() : array
    {
        return $this->user->servers()->orderBy('name', 'asc')->get()->map(fn ($server) : array => [
            'provider'              => $server->provider,
            'name'                  => $server->name,
            'host'                  => $server->host,
            'process_type'          => $server->process_type,
            'uses_bip38_encryption' => $server->uses_bip38_encryption,
            'auth'                  => $server->usesAccessKey() ? [
                'access_key' => $server->auth_access_key,
            ] : array_filter([
                'username' => $server->auth_username,
                'password' => $server->auth_password,
            ]),
        ])->toArray();
    }

    /**
     * Generate the downloaded filename for the export request.
     *
     * @return string
     */
    private function filename() : string
    {
        return sprintf('Nodem_Servers_%s.json', now()->format('Y_m_d'));
    }
}
