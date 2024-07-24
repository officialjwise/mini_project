@php
    $plan = Auth::user()->activePlan();
    $plan_type = 'regular';
    // $team = Auth::user()->getAttribute('team');
    $teamManager = Auth::user()->getAttribute('teamManager');

    if ($plan != null) {
        $plan_type = strtolower($plan->plan_type);
    }

    $titlebar_links = [
        [
            'label' => 'All',
            'link' => '#all',
        ],
        [
            'label' => 'AI Assistant',
            'link' => '#all',
        ],
        [
            'label' => 'Your Plan',
            'link' => '#plan',
        ],
        [
            'label' => 'Team Members',
            'link' => '#team',
        ],
        [
            'label' => 'Recent',
            'link' => '#recent',
        ],
        [
            'label' => 'Documents',
            'link' => '#documents',
        ],
        [
            'label' => 'Templates',
            'link' => '#templates',
        ],
        [
            'label' => 'Overview',
            'link' => '#all',
        ],
    ];

@endphp

@push('css')
    <style>
        @if (setting('announcement_background_color'))
            .lqd-card.lqd-announcement-card {
                background-color: {{ setting('announcement_background_color') }};
            }
        @endif
        @if (setting('announcement_background_image'))
            .lqd-card.lqd-announcement-card {
                background-image: url({{ setting('announcement_background_image') }});
            }
        @endif
        @if (setting('announcement_background_color_dark'))
            .theme-dark .lqd-card.lqd-announcement-card {
                background-color: {{ setting('announcement_background_color_dark') }};
            }
        @endif
        @if (setting('announcement_background_image_dark'))
            .theme-dark .lqd-card.lqd-announcement-card {
                background-image: url({{ setting('announcement_background_image_dark') }});
            }
        @endif
    </style>
@endpush

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Dashboard'))
@section('titlebar_title')
    {{ __('Welcome') }}, {{ auth()->user()->name }}.
@endsection



@section('content')
    <div class="flex flex-wrap justify-between gap-8 py-5">
        <div
            class="grid w-full grid-cols-1 gap-10"
            id="all"
        >
            @if (setting('announcement_active', 0) && !auth()->user()->dash_notify_seen)
                <div
                    class="lqd-announcement"
                    x-data="{ show: !localStorage.getItem('lqd-announcement-dismissed') }"
                    x-ref="announcement"
                >
                    <script>
                        const announcementDismissed = localStorage.getItem('lqd-announcement-dismissed');
                        if (announcementDismissed) {
                            document.querySelector('.lqd-announcement').style.display = 'none';
                        }
                    </script>

                    <x-card
                        class="lqd-announcement-card relative bg-cover bg-center"
                        size="lg"
                        x-ref="announcementCard"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="mb-3">
                                    @lang(setting('announcement_title', 'Welcome'))
                                </h3>
                                <p class="mb-4">
                                    @lang(setting('announcement_description', 'We are excited to have you here. Explore the marketplace to find the best AI models for your needs.'))
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <x-button
                                        class="font-medium"
                                        href="{{ setting('announcement_url', '#') }}"
                                    >
                                        <x-tabler-plus class="size-4" />
                                        {{ setting('announcement_button_text', 'Try it Now') }}
                                    </x-button>
                                    <x-button
                                        class="font-medium"
                                        href="javascript:void(0)"
                                        variant="ghost-shadow"
                                        hover-variant="danger"
                                        @click.prevent="dismiss()"
                                    >
                                        @lang('Dismiss')
                                    </x-button>
                                </div>
                            </div>
                            @if (setting('announcement_image_dark'))
                                <img
                                    class="announcement-img announcement-img-dark peer hidden w-28 shrink-0 dark:block"
                                    src="{{ setting('announcement_image_dark', '/upload/images/speaker.png') }}"
                                    alt="@lang(setting('announcement_title', 'Welcome to J-Wise AI!'))"
                                >
                            @endif
                            <img
                                class="announcement-img announcement-img-light w-28 shrink-0 dark:peer-[&.announcement-img-dark]:hidden"
                                src="{{ setting('announcement_image', '/upload/images/speaker.png') }}"
                                alt="@lang(setting('announcement_title', 'Welcome to J-Wise AI!'))"
                            >
                        </div>
                    </x-card>
                </div>
            @endif

        </div>

        @if ($ongoingPayments != null)
            <div class="w-full">
                @include('panel.user.finance.ongoingPayments')
            </div>
        @endif

        



        <x-card
            class="w-full"
            id="recent"
            size="lg"
        >
            <h3 class="mb-7">
                @lang('Recent Activities')
            </h3>

            <div
                class="lqd-docs-container group"
                data-view-mode="grid"
            >
                <div class="lqd-docs-list grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    @foreach (Auth::user()->openai()->orderBy('updated_at', 'desc')->take(5)->get() as $entry)
                        @if ($entry->generator != null)
                            <x-documents.item
                                :$entry
                                style="extended"
                                trim="100"
                                hide-fav
                            />
                        @endif
                    @endforeach
                </div>
            </div>
        </x-card>

        <div
            class="grow basis-full md:basis-0"
            id="documents"
        >
            <x-card size="none">
                <x-slot:head>
                    <h4 class="m-0">{{ __('Documents') }}</h4>
                </x-slot:head>
                @foreach (Auth::user()->openai()->with('generator')->take(4)->get() as $entry)
                    @if ($entry->generator != null)
                        <x-documents.item :$entry />
                    @endif
                @endforeach
            </x-card>
        </div>

        <div
            class="grow basis-full md:basis-0"
            id="templates"
        >
            <x-card size="none">
                <x-slot:head>
                    <h4 class="m-0">{{ __('Favorite Templates') }}</h4>
                </x-slot:head>
                @foreach (\Illuminate\Support\Facades\Auth::user()->favoriteOpenai as $entry)
                    @php
                        $upgrade = false;
                        if ($entry->premium == 1 && $plan_type === 'regular') {
                            $upgrade = true;
                        }

                        if ($upgrade) {
                            $href = LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription'));
                        } else {
                            $href = LaravelLocalization::localizeUrl(
                                route($entry->type === 'voiceover' ? 'dashboard.user.openai.generator.workbook' : 'dashboard.user.openai.generator', $entry->slug),
                            );
                        }
                    @endphp
                    @if ($upgrade || $entry->active == 1)
                        <a
                            class="lqd-fav-temp-item relative flex w-full flex-wrap items-center gap-3 border-b p-4 text-xs transition-colors last:border-none hover:bg-foreground/5"
                            href="{{ $href }}"
                        >
                        @else
                            <p class="lqd-fav-temp-item relative flex w-full flex-wrap items-center gap-3 border-b p-4 text-xs last:border-none">
                    @endif
                    <x-lqd-icon
                        size="lg"
                        style="background: {{ $entry->color }}"
                        active-badge
                        active-badge-condition="{{ $entry->active == 1 }}"
                    >
                        <span class="size-5 flex">
                            @if ($entry->image !== 'none')
                                {!! html_entity_decode($entry->image) !!}
                            @endif
                        </span>
                    </x-lqd-icon>
                    <span class="w-2/5 grow">
                        <span class="lqd-fav-temp-item-title block text-sm font-medium">
                            {{ __($entry->title) }}
                        </span>
                        <span class="lqd-fav-temp-item-desc opacity-45 block max-w-full overflow-hidden overflow-ellipsis whitespace-nowrap italic">
                            {{ str()->words(__($entry->description), 5) }}
                        </span>
                    </span>
                    <span class="flex flex-col whitespace-nowrap">
                        {{ __('in Workbook') }}
                        <span class="lqd-fav-temp-item-date opacity-45 italic">
                            {{ $entry->created_at->format('M d, Y') }}
                        </span>
                    </span>
                    @if ($upgrade)
                        <span class="absolute inset-0 flex items-center justify-center bg-background/50">
                            <x-badge
                                class="rounded-md py-1.5"
                                variant="info"
                            >
                                {{ __('Upgrade') }}
                            </x-badge>
                        </span>
                    @endif
                    @if ($upgrade || $entry->active == 1)
                        </a>
                    @else
                        </p>
                    @endif
                    @if ($loop->iteration == 4)
                    @break
                @endif
            @endforeach
        </x-card>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (window.innerWidth >= 768) {
            const steps = @json(\App\Models\Introduction::getFormattedSteps());

            @if (auth()->user()->tour_seen == 0 && \App\Models\Setting::first()->tour_seen == 1)
                introJs().setOptions({
                    showBullets: false,
                    steps: steps.map(step => {
                        step.element = document.querySelector(step.element);
                        return step;
                    })
                }).oncomplete(function() {
                    fetch('/dashboard/user/mark-tour-seen', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    });
                }).start();
            @endif
        }
    });

    function dismiss() {
        // localStorage.setItem('lqd-announcement-dismissed', true);
        document.querySelector('.lqd-announcement').style.display = 'none';
        $.ajax({
            url: '{{ route('dashboard.user.dash_notify_seen') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                /* console.log(response); */
            }
        });
    }
</script>
@endpush
