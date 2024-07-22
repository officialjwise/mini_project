@php
    $filters = ['All', \App\Enums\AITokenType::WORD->value];
@endphp

@extends('panel.layout.settings')
@section('title', __('AI Chat Models'))
@section('titlebar_actions', '')
@section('settings')
    <div x-data="{ 'activeFilter': 'All' }">
        <form
            class="flex flex-col gap-5"
            id="cost_form"v
            action="{{ route('dashboard.admin.ai-chat-model.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            <h4 class="mb-0">
                {{ __('Editing: AI Chat Models') }}
            </h4>
            <label>
                {{ __('Manage available AI models visible to users within AI chat: Control the selection and presentation of AI models accessible to users during chat interactions.') }}
            </label>

            <ul class="flex w-full justify-between gap-3 rounded-full bg-foreground/10 p-1 text-xs font-medium">
                @foreach ($filters as $filter)
                    <li>
                        <button
                            @class([
                                'px-6 py-3 leading-tight rounded-full transition-all hover:bg-background/80 [&.lqd-is-active]:bg-background [&.lqd-is-active]:shadow-[0_2px_12px_hsl(0_0%_0%/10%)]',
                                'lqd-is-active' => $loop->first,
                            ])
                            @click="activeFilter = '{{ $filter }}'"
                            :class="{ 'lqd-is-active': activeFilter == '{{ $filter }}' }"
                            type="button"
                        >
                            {{ ucfirst($filter) }}
                        </button>
                    </li>
                @endforeach
            </ul>

            @php
                $index = 0;
            @endphp
            @foreach ($groupedAiModels as $category => $groupedAiModel)
                @php
                    $formattedCategory = ucwords(str_replace('_', ' ', $category));
                    $index++;
                @endphp
                <x-form-step
                    step="{{ $index }}"
                    label="{{ ucfirst($formattedCategory) }}"
                />

                @foreach ($groupedAiModel as $aiModel)
                    @foreach ($aiModel->tokens as $aiToken)
                        <x-card
                            data-cat="{{ $aiToken->type }}"
                            size="none"
                            variant="shadow"
                            class="p-2"
                            ::class="{ 'hidden': !$el.getAttribute('data-cat')?.includes(activeFilter) && activeFilter !== 'All' }"
                        >
                            <x-forms.input
                                type="text"
                                name="selected_title[{{ $aiToken->ai_model_id }}]"
                                value="{{ $aiModel->selected_title }}"
                                label="{{ __($aiModel->key) }}"
                                tooltip="{{ __($aiModel->title) }}"
                                labelExtra=""
                                switcher
                            >
                                <x-forms.input
                                        class:container="mb-2 mt-2"
                                        id="is_selected_{{ $aiToken->ai_model_id }}"
                                        type="checkbox"
                                        name="is_selected[{{ $aiToken->ai_model_id }}]"
                                        :checked="$aiModel?->is_selected == 1"
                                        label="{{ __('Enable for Users') }}"
                                        switcher
                                />
                            </x-forms.input>
                        </x-card>
                    @endforeach
                @endforeach
            @endforeach

            @if ($app_is_demo)
                <x-button
                    type="button"
                    onclick="return toastr.info('This feature is disabled in Demo version.');"
                >
                    {{ __('Save') }}
                </x-button>
            @else
                <x-button
                    id="cost_button"
                    type="submit"
                    form="cost_form"
                >
                    {{ __('Save') }}
                </x-button>
            @endif
        </form>
    </div>
@endsection
