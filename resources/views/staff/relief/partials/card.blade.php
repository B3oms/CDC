<div class="relief-card-wrapper">
    <a href="{{ route('staff.relief.show', $event->id) }}" class="relief-card status-{{ strtolower($event->status) }}">
        <div class="relief-card-header">
            <span class="relief-card-name">{{ $event->name }}</span>
            <span class="relief-status-badge {{ strtolower($event->status) }}">{{ $event->status }}</span>
        </div>
        <div class="relief-card-meta">
            <div class="meta-row">
                <span class="meta-label">Date</span>
                <span>{{ is_string($event->date) ? date('M d, Y', strtotime($event->date)) : \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Venue</span>
                <span>{{ $event->venue }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Barangays</span>
                <span>
                    @if($event->eventBarangays->isEmpty())
                        No barangays assigned
                    @else
                        @foreach($event->eventBarangays as $eb)
                            {{ $eb->barangay->name }} ({{ $eb->beneficiary_count ?? 0 }} beneficiaries)@if(!$loop->last), @endif
                        @endforeach
                    @endif
                </span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Calamity</span>
                <span>{{ $event->calamity ? $event->calamity->name : 'No Calamity' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Created By</span>
                <span>{{ $event->creator ? $event->creator->first_name . ' ' . $event->creator->last_name : 'Unknown' }}</span>
            </div>
        </div>
        <div class="relief-card-footer">
            <div class="status-button-container">
                @if($event->status === 'Ongoing')
                    <button class="status-btn status-ongoing" onclick="event.preventDefault(); window.location.href='{{ route('staff.relief.show', $event->id) }}'">
                        <i class="fas fa-check-circle"></i>
                        <span>Mark as Finished</span>
                    </button>
                @endif
            </div>
            <div class="view-details">View Details →</div>
        </div>
        
        {{-- Delete Button - Inside the card --}}
        @if($event->status !== 'Ongoing')
        <form action="{{ route('staff.relief.destroy', $event->id) }}" method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this relief event? This action cannot be undone.')" 
              class="delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-btn" title="Delete Event">
                <i class="fas fa-trash"></i>
            </button>
        </form>
        @endif
    </a>
</div>
