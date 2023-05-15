<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CreateNewUser;
use ARKEcosystem\Foundation\Fortify\Components\RegisterForm as Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

final class RegisterForm extends Component
{
    public string $code = '';

    public function mount(): void
    {
        parent::mount();

        $this->code = old('code', '');
    }

    public function render(): View
    {
        return view('livewire.register-form');
    }

    public function updated(string $propertyName, mixed $value): void
    {
        if ($propertyName === 'username') {
            session()->put('username', $value);
            $this->validateOnly('username');
            $this->validateOnly('code');

            return;
        }

        if ($propertyName === 'password_confirmation') {
            $this->validateOnly('password_confirmation', ['password' => 'confirmed']);
        }

        $values = [$propertyName => $value];
        $rules  = [$propertyName => $this->rules()[$propertyName]];

        if ($propertyName === 'password') {
            $values['password_confirmation'] = $this->password_confirmation;
            $rules['password_confirmation']  = $this->rules()['password_confirmation'];

            $this->resetErrorBag([$propertyName, 'password_confirmation']);
        } elseif ($propertyName === 'password_confirmation') {
            $values['password'] = $this->password;
        }

        $validator = Validator::make($values, $rules);

        if ($validator->fails()) {
            $this->setErrorBag(
                $validator->getMessageBag()->merge($this->getErrorBag())
            );

            return;
        }

        $this->resetErrorBag($propertyName);
    }

    public function canSubmit(): bool
    {
        if ($this->code === '') {
            return false;
        }

        if ($this->username === '') {
            return false;
        }

        if ($this->password === '') {
            return false;
        }

        if ($this->password_confirmation === '') {
            return false;
        }

        if (! $this->terms) {
            return false;
        }

        return $this->getErrorBag()->count() === 0;
    }

    protected function rules(): array
    {
        return collect(CreateNewUser::createValidationRules())
            ->filter(fn ($value, $key) => property_exists($this, $key))
            ->toArray();
    }
}
