@extends('staff.layouts.app')
@section('title', 'Add Calamity Event')

@section('content')

<style>
.cr-wrap{max-width:860px;margin:0 auto;padding:0 0 3rem;font-family:'Segoe UI', sans-serif}
.cr-wrap *,.cr-wrap *::before,.cr-wrap *::after{box-sizing:border-box}

/* Form Styles */
.cr-error{display:flex;gap:12px;align-items:flex-start;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#991b1b;font-size:14px}
.cr-error ul{margin:4px 0 0;padding-left:1.25rem}
.cr-form{display:flex;flex-direction:column;gap:12px}
.cr-card{background:#fff;border:1px solid #d3d1c7;border-radius:10px;overflow:hidden}
.cr-sec-label{font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#888780;padding:1.25rem 1.5rem .75rem;display:flex;align-items:center;gap:10px}
.cr-sec-label::after{content:'';flex:1;height:1px;background:#f1efe8}
.cr-sec-aside{font-size:12px;font-weight:400;letter-spacing:0;text-transform:none;color:#888780;margin-left:auto}
.cr-sec-aside strong{color:#2c2c2a;font-weight:600}
.cr-body{padding:0 1.5rem 1.5rem}
.cr-row{display:grid;gap:12px;margin-bottom:12px}
.cr-row:last-child{margin-bottom:0}
.cr-row-3{grid-template-columns:2fr 1fr 1fr}
.cr-row-half{grid-template-columns:1fr 1fr}
.cr-field{display:flex;flex-direction:column;gap:5px}
.cr-field label{font-size:12px;font-weight:500;color:#2c2c2a;display:block}
.cr-req{color:#e24b4a}
.cr-field input[type=text],.cr-field input[type=date],.cr-field select,.cr-field textarea{padding:8px 11px;border:1px solid #d3d1c7;border-radius:6px;background:#fff;color:#2c2c2a;font-size:14px;width:100%;transition:border-color .15s}
.cr-field input[type=text]:focus,.cr-field input[type=date]:focus,.cr-field select:focus,.cr-field textarea:focus{outline:none;border-color:#1a3d1f;box-shadow:0 0 0 3px rgba(26,61,31,.08)}
.cr-field input::placeholder{color:#888780}
.cr-muni-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:8px;margin-bottom:16px}
.cr-muni-tile{padding:11px 14px;border:1px solid #d3d1c7;border-radius:8px;cursor:pointer;background:#fff;user-select:none}
.cr-muni-tile:hover{background:#f1efe8;border-color:#888780}
.cr-muni-tile.cr-muni-on{border-color:#1a3d1f;background:#f1efe8}
.cr-muni-name{font-size:13px;font-weight:600;color:#2c2c2a}
.cr-muni-prov{font-size:11px;color:#888780}
.cr-bgy-label{font-size:11px;font-weight:500;letter-spacing:.07em;text-transform:uppercase;color:#888780;margin-bottom:10px}
.cr-bgy-grid{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:12px}
.cr-bgy-chip{display:inline-flex;flex-direction:column;padding:8px 14px;border:1px solid #d3d1c7;border-radius:8px;cursor:pointer;background:#fff;user-select:none}
.cr-bgy-chip:hover{background:#f1efe8;border-color:#888780}
.cr-bgy-chip.cr-bgy-on{border-color:#1a3d1f;background:#f1efe8}
.cr-bgy-name{font-size:13px;font-weight:500;color:#2c2c2a}
.cr-bgy-ct{font-size:11px;color:#888780;margin-top:1px}
.cr-bgy-hint{font-size:13px;color:#888780;display:flex;align-items:center;gap:6px;padding:4px 0}
.cr-footer{display:flex;justify-content:flex-end;align-items:center;gap:8px;padding-top:4px}
.cr-btn-cancel{padding:9px 18px;border:1px solid #d3d1c7;border-radius:6px;background:#fff;color:#5f5e5a;font-size:14px;font-weight:500;text-decoration:none;display:inline-block}
.cr-btn-cancel:hover{background:#f1efe8;color:#2c2c2a}
.cr-btn-submit{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border:none;border-radius:6px;background:#1a3d1f;color:#fff;font-size:14px;font-weight:500;cursor:pointer}
.cr-btn-submit:hover{background:#145222}

/* Header Styles */
.cr-top-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.75rem;padding:1.5rem 0 1.25rem;border-bottom:1px solid #d3d1c7}
.cr-header-left{flex:1}
.cr-header-right{display:flex;align-items:center;gap:1rem}
</style>

<div class="cr-wrap">
  @if($errors->any())
    <div class="cr-error">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @if(in_array(auth()->user()->role->name, ['Admin', 'Staff']))
  <form method="POST" action="{{ route('staff.calamities.store') }}" class="cr-form">
    @csrf

    {{-- Calamity details --}}
    <div class="cr-card">
      <div class="cr-sec-label">Calamity details</div>
      <div class="cr-body">
        <div class="cr-row cr-row-half">
          <div class="cr-field">
            <label>Calamity name <span class="cr-req">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Typhoon Carina" required>
          </div>
          <div class="cr-field">
            <label>Type <span class="cr-req">*</span></label>
            <select name="type" required>
              <option value="">Select type</option>
              <option value="Typhoon" {{ old('type') == 'Typhoon' ? 'selected' : '' }}>Typhoon</option>
              <option value="Flood" {{ old('type') == 'Flood' ? 'selected' : '' }}>Flood</option>
              <option value="Earthquake" {{ old('type') == 'Earthquake' ? 'selected' : '' }}>Earthquake</option>
              <option value="Landslide" {{ old('type') == 'Landslide' ? 'selected' : '' }}>Landslide</option>
              <option value="Volcanic Eruption" {{ old('type') == 'Volcanic Eruption' ? 'selected' : '' }}>Volcanic Eruption</option>
              <option value="Fire" {{ old('type') == 'Fire' ? 'selected' : '' }}>Fire</option>
              <option value="Industrial Accident" {{ old('type') == 'Industrial Accident' ? 'selected' : '' }}>Industrial Accident</option>
            </select>
          </div>
        </div>
        <div class="cr-row cr-row-half" style="margin-bottom:0">
          <div class="cr-field">
            <label>Date occurred <span class="cr-req">*</span></label>
            <input type="date" name="date_occurred" value="{{ old('date_occurred') }}" placeholder="Select date" required>
          </div>
        </div>
      </div>
    </div>

    {{-- Description --}}
    <div class="cr-card">
      <div class="cr-sec-label">Description</div>
      <div class="cr-body">
        <div class="cr-field">
          <label>Calamity description</label>
          <textarea name="description" rows="4" placeholder="Provide a detailed description of the calamity event...">{{ old('description') }}</textarea>
        </div>
      </div>
    </div>

    {{-- Partner Barangays --}}
    <div class="cr-card">
      <div class="cr-sec-label">Partner Barangays</div>
      <div class="cr-body">
        <div class="cr-muni-grid">
          @foreach($municipalities as $muni)
          <div class="cr-muni-tile {{ in_array($muni->id, old('municipality_ids',[])) ? 'cr-muni-on' : '' }}"
            onclick="crToggleMuni(this, {{ $muni->id }})">
            <input type="checkbox" name="municipality_ids[]" value="{{ $muni->id }}"
              id="muni-{{ $muni->id }}" style="display:none"
              {{ in_array($muni->id, old('municipality_ids',[])) ? 'checked' : '' }}>
            <div class="cr-muni-name">{{ $muni->name }}</div>
            <div class="cr-muni-prov">{{ $muni->province }}</div>
          </div>
          @endforeach
        </div>

        <div id="cr-bgy-sec" style="display:none">
          <div class="cr-bgy-label">Barangays</div>
          <div id="cr-bgy-grid" class="cr-bgy-grid"></div>
          <div id="cr-benef-pill" class="cr-benef-pill">
            <i class="fas fa-users"></i>
            <span><strong id="cr-benef-count">0</strong> barangays selected</span>
          </div>
        </div>
        <p id="cr-bgy-hint" class="cr-bgy-hint">
          <i class="fas fa-map-marker-alt"></i> Select a municipality above to choose barangays
        </p>
      </div>
    </div>

    {{-- Form Actions --}}
    <div class="cr-footer">
      <x-back-button href="{{ route('staff.calamities.index') }}" label="Back" />
      <button type="submit" class="cr-btn-submit">
        <i class="fas fa-plus"></i>
        Create
      </button>
    </div>
  </form>
  @else
  <div class="cr-card">
    <div class="cr-body" style="text-align:center;padding:3rem;">
      <p style="color:#888;font-size:16px;">Only administrators can create calamity events.</p>
      <x-back-button href="{{ route('staff.calamities.index') }}" style="margin-top:1rem;" label="Back to Calamities" />
    </div>
  </div>
  @endif
</div>

<script>
/* ── Municipality tiles ──────────────────────────── */
var crMuniData = @json($municipalities->load('barangays'));
var crSelectedMunis = {};

function crToggleMuni(tile, muniId) {
  var chk = tile.querySelector('input[type=checkbox]');
  var wasOn = tile.classList.contains('cr-muni-on');
  
  if (wasOn) {
    tile.classList.remove('cr-muni-on');
    chk.checked = false;
    delete crSelectedMunis[muniId];
  } else {
    tile.classList.add('cr-muni-on');
    chk.checked = true;
    var muni = crMuniData.find(m => m.id == muniId);
    if (muni) crSelectedMunis[muniId] = muni;
  }
  
  crRenderBarangays();
  crUpdateBenef();
}

function crRenderBarangays() {
  var grid = document.getElementById('cr-bgy-grid');
  var sec = document.getElementById('cr-bgy-sec');
  var hint = document.getElementById('cr-bgy-hint');
  
  var keys = Object.keys(crSelectedMunis);
  if (keys.length === 0) {
    sec.style.display = 'none';
    hint.style.display = 'block';
    return;
  }
  
  sec.style.display = 'block';
  hint.style.display = 'none';
  
  var html = '';
  keys.forEach(function(id) {
    var muni = crSelectedMunis[id];
    if (muni && muni.barangays) {
      muni.barangays.forEach(function(b) {
        html += '<div class="cr-bgy-chip" data-b="' + b.id + '" onclick="crToggleBgy(this)">'
              + '<div class="cr-bgy-name">' + b.name + '</div>'
              + '<div class="cr-bgy-ct">' + b.municipality_name + '</div>'
              + '<input type="hidden" name="barangay_ids[]" value="' + b.id + '" disabled>'
              + '</div>';
      });
    }
  });
  grid.innerHTML = html || '<span style="font-size:13px;color:#9ca3af">No barangays found.</span>';
  crUpdateBenef();
}

function crToggleBgy(chip) {
  var wasOn = chip.classList.contains('cr-bgy-on');
  var hid = chip.querySelector('input[type=hidden]');
  
  if (wasOn) {
    chip.classList.remove('cr-bgy-on');
    chip.classList.remove('cr-bgy-on');
    hid.disabled = true;
  } else {
    chip.classList.add('cr-bgy-on');
    hid.disabled = false;
  }
  
  crUpdateBenef();
}

function crUpdateBenef() {
  var chips = document.querySelectorAll('#cr-bgy-grid .cr-bgy-chip');
  var count = 0;
  chips.forEach(function(c) {
    if (!c.querySelector('input[type=hidden]').disabled) count++;
  });
  document.getElementById('cr-benef-count').textContent = count;
  
  var pill = document.getElementById('cr-benef-pill');
  if (count > 0) {
    pill.style.display = 'flex';
  } else {
    pill.style.display = 'none';
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Restore selected municipalities from old input if any
  var oldMunis = @json(old('municipality_ids', []));
  oldMunis.forEach(function(muniId) {
    var tile = document.querySelector('.cr-muni-tile[onclick*="' + muniId + '"]');
    if (tile) {
      crToggleMuni(tile, muniId);
    }
  });
  
  // Restore selected barangays from old input if any
  var oldBgyIds = @json(old('barangay_ids', []));
  setTimeout(function() {
    oldBgyIds.forEach(function(bgyId) {
      var chip = document.querySelector('.cr-bgy-chip[data-b="' + bgyId + '"]');
      if (chip) {
        crToggleBgy(chip);
      }
    });
  }, 100);
});
</script>

@endsection
