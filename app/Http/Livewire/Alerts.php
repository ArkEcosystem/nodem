<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Cache\AlertStore;
use App\Models\User;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Livewire\Component;

final class Alerts extends Component
{
    use InteractsWithUser;

    public function fetch(): void
    {
        foreach ($this->getAlerts() as $name => $alerts) {
            foreach ($alerts as $type => $messages) {
                $this->alert($this->formatMessages($messages, $name), $type);
            }
        }
    }

    private function getAlerts(): array
    {
        /** @var User $user */
        $user = $this->user;

        $alerts = [];
        foreach (AlertStore::pullAll($user) as $alert) {
            $typeAlerts = $alerts[$alert->serverName()][$alert->type()] ?? [];
            if (! in_array($alert->message(), $typeAlerts, true)) {
                $alerts[$alert->serverName()][$alert->type()][] = $alert->message();
            }
        }

        return $alerts;
    }

    private function alert(string $message, string $type): void
    {
        $this->emit('toastMessage', [$message, $type]);
    }

    private function formatMessages(mixed $messages, string $name): string
    {
        $output = "<strong>{$name}</strong><ul class='ml-3 list-disc'>";

        foreach ($messages as $message) {
            $output .= "<li>{$message}</li>";
        }

        $output .= '</ul>';

        return $output;
    }
}
