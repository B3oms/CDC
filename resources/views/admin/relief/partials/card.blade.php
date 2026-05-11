<a href="{{ route('admin.relief.show', $event->id) }}" class="relief-card status-{{ strtolower($event->status) }}">
    <div class="relief-card-header">
        <span class="relief-card-name">{{ $event->name }}</span>
        <span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span>
    </div>
    <div class="relief-card-meta">
        <div class="meta-row">
            <span class="meta-label">Date</span>
            <span>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Venue</span>
            <span>{{ $event->venue }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Barangays</span>
            <span>
                @foreach($event->eventBarangays as $eb)
                    {{ $eb->barangay->name }}@if(!$loop->last), @endif
                @endforeach
            </span>
        </div>
    </div>
    <div class="relief-card-footer">View Details →</div>
</a>