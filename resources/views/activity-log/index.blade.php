@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Activity Log" subtitle="Recent admin changes across the system" />

        <x-form-card>
            @if ($activities->isEmpty())
                <x-empty-state message="No activity recorded yet." icon="fa-history" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Subject</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at?->format('d M Y H:i') }}</td>
                                    <td>{{ $activity->causer?->name ?? 'System' }}</td>
                                    <td><span class="badge badge-info">{{ $activity->event ?? '-' }}</span></td>
                                    <td>
                                        @if ($activity->subject_type)
                                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $activities->links() }}
                </div>
            @endif
        </x-form-card>
    </div>
</section>
@endsection
