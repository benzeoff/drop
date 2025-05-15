<x-filament::page>
    <h1 class="text-2xl mb-4">Создание матчей для турнира: {{ $tournament->name }}</h1>

    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Создать матчи
        </x-filament::button>
    </form>
</x-filament::page>
