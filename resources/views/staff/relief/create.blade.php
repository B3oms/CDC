@extends('staff.layouts.app')
@section('title', 'Create Relief Event')
@section('breadcrumb', 'Create Event')

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
.cr-field input[type=text],.cr-field input[type=date],.cr-field select{padding:8px 11px;border:1px solid #d3d1c7;border-radius:6px;background:#fff;color:#2c2c2a;font-size:14px;width:100%;transition:border-color .15s}
.cr-field input[type=text]:focus,.cr-field input[type=date]:focus,.cr-field select:focus{outline:none;border-color:#1a3d1f;box-shadow:0 0 0 3px rgba(26,61,31,.08)}
.cr-field input::placeholder{color:#888780}
.cr-acc-group{border-bottom:1px solid #f1efe8}
.cr-acc-group:last-child{border-bottom:none}
.cr-acc-toggle{width:100%;display:flex;align-items:center;gap:10px;padding:12px 1.5rem;background:none;border:none;cursor:pointer;text-align:left}
.cr-acc-toggle:hover{background:#f1efe8}
.cr-pill{font-size:10px;font-weight:700;letter-spacing:.05em;padding:2px 8px;border-radius:20px;flex-shrink:0}
.cr-pill-staff{background:#1a3d1f;color:#9fe1cb}
.cr-pill-vol{background:#145222;color:#9fe1cb}
.cr-pill-bar{background:#f5c300;color:#1a3d1f}
.cr-acc-name{font-size:13px;font-weight:500;color:#2c2c2a;flex:1}
.cr-chev{font-size:11px;color:#888780;transition:transform .2s;display:inline-block}
.cr-chev.open{transform:rotate(180deg)}
.cr-acc-body{display:none;padding:.75rem 1.5rem 1rem}
.cr-acc-body.open{display:block}
.cr-chip-grid{display:flex;flex-wrap:wrap;gap:7px}
.cr-chip{display:inline-flex;align-items:center;gap:8px;padding:8px 12px 8px 8px;border:1px solid #d3d1c7;border-radius:6px;cursor:pointer;background:#fff;user-select:none}
.cr-chip:hover{background:#f1efe8;border-color:#888780}
.cr-chip.cr-chip-on{border-color:#1a3d1f;background:#f1efe8}
.cr-chip input[type=checkbox]{display:none}
.cr-av{width:28px;height:28px;border-radius:2px !important;background:#f1efe8;color:#2c2c2a;font-size:11px;font-weight:500;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cr-chip.cr-chip-on .cr-av{background:#1a3d1f;color:#fff}
.cr-chip-name{font-size:13px;font-weight:500;color:#2c2c2a;line-height:1.3}
.cr-chip-sub{font-size:11px;color:#888780}
.cr-empty{font-size:13px;color:#888780;padding:4px 0}
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
.cr-benef-pill{display:none;align-items:center;gap:7px;padding:7px 14px;background:#f1efe8;border-radius:100px;font-size:13px;color:#2c2c2a;margin-top:4px}
.cr-benef-pill strong{color:#2c2c2a;font-weight:600}
.cr-inv-list{border:1px solid #d3d1c7;border-radius:8px;overflow:hidden}
.cr-inv-row{display:grid;grid-template-columns:1fr 110px;align-items:center;padding:11px 14px;border-bottom:1px solid #f1efe8;gap:12px}
.cr-inv-row:last-child{border-bottom:none}
.cr-inv-row:hover{background:#f1efe8}
.cr-inv-check{display:flex;align-items:center;gap:10px;cursor:pointer}
.cr-inv-check input[type=checkbox]{width:14px;height:14px;flex-shrink:0;accent-color:#1a3d1f}
.cr-inv-name{font-size:13px;font-weight:500;color:#2c2c2a}
.cr-inv-cat{font-size:11px;color:#888780}
.cr-inv-stock{text-align:right}
.cr-stk-n{font-size:14px;font-weight:500;color:#2c2c2a}
.cr-stk-u{font-size:11px;color:#888780}
.cr-inv-res{text-align:right;gap:6px;display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap}
.cr-res-val{font-size:13px;font-weight:500;color:#065f46}
.cr-inv-res.warn .cr-res-val{color:#e24b4a}
.cr-qty-input{width:60px;padding:6px;border:1px solid #d3d1c7;border-radius:4px;font-size:12px;text-align:center}
.cr-qty-input:focus{outline:none;border-color:#1a3d1f}
.cr-calc-display{font-size:10px;color:#065f46;font-weight:500;margin-left:8px;white-space:nowrap;line-height:1.2}
.cr-dist-ttl{font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:#888780;margin-bottom:10px}
.cr-sum-row{display:flex;justify-content:space-between;font-size:13px;padding:5px 0;border-bottom:1px solid #f1efe8;color:#2c2c2a}
.cr-sum-row:last-child{border-bottom:none}
.cr-sum-row.warn .cr-sum-st{color:#e24b4a}
.cr-sum-st{color:#888780}
.cr-dist-table{width:100%;border-collapse:collapse;font-size:12px;margin-top:0.5rem}
.cr-dist-table th{background:#e8e4d9;padding:8px;text-align:left;font-weight:600;color:#2c2c2a;border-bottom:2px solid #d3d1c7}
.cr-dist-table td{padding:8px;border-bottom:1px solid #e8e4d9;color:#2c2c2a}
.cr-dist-table tr:last-child td{border-bottom:none}
.cr-footer{display:flex;justify-content:flex-end;align-items:center;gap:8px;padding-top:4px}
.cr-btn-cancel{padding:9px 18px;border:1px solid #d3d1c7;border-radius:6px;background:#fff;color:#5f5e5a;font-size:14px;font-weight:500;text-decoration:none;display:inline-block}
.cr-btn-cancel:hover{background:#f1efe8;color:#2c2c2a}
.cr-btn-submit{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border:none;border-radius:6px;background:#1a3d1f;color:#fff;font-size:14px;font-weight:500;cursor:pointer}
.cr-btn-submit:hover{background:#145222}

/* Header Styles */
.cr-top-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.75rem;padding:1.5rem 0 1.25rem;border-bottom:1px solid #d3d1c7}
.cr-header-left{flex:1}
.cr-header-right{display:flex;align-items:center;gap:1rem}

/* Notifications */
.cr-notifications{position:relative}
.cr-notification-btn{background:none;border:none;font-size:1.1rem;color:#5f5e5a;cursor:pointer;padding:0.5rem;border-radius:4px;transition:background .2s;position:relative}
.cr-notification-btn:hover{background:#f1efe8}
.cr-notification-badge{position:absolute;top:0;right:0;background:#e24b4a;color:#fff;font-size:.7rem;font-weight:700;padding:2px 5px;border-radius:10px;min-width:16px;text-align:center}
.cr-notification-dropdown{position:absolute;top:100%;right:0;background:#fff;border:1px solid #d3d1c7;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.1);width:320px;max-height:400px;overflow-y:auto;z-index:1000;opacity:0;visibility:hidden;transform:translateY(-10px);transition:all .2s}
.cr-notification-dropdown.show{opacity:1;visibility:visible;transform:translateY(0)}
.cr-notification-header{display:flex;justify-content:space-between;align-items:center;padding:1rem;border-bottom:1px solid #f1efe8}
.cr-notification-header h4{font-size:.9rem;font-weight:700;color:#2c2c2a;margin:0}
.cr-mark-all-read{background:none;border:none;color:#185fa5;font-size:.8rem;cursor:pointer;text-decoration:underline}
.cr-notification-list{padding:.5rem 0}
.cr-notification-item{display:flex;gap:.75rem;padding:.75rem 1rem;transition:background .2s;cursor:pointer}
.cr-notification-item:hover{background:#f1efe8}
.cr-notification-item.unread{background:#f8f9ff;border-left:3px solid #185fa5}
.cr-notification-icon{flex-shrink:0;width:32px;height:32px;border-radius:50%;background:#f1efe8;display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#5f5e5a}
.cr-notification-content{flex:1;min-width:0}
.cr-notification-title{font-size:.85rem;font-weight:600;color:#2c2c2a;margin-bottom:2px}
.cr-notification-text{font-size:.8rem;color:#888780;margin-bottom:2px;line-height:1.3}
.cr-notification-time{font-size:.75rem;color:#b4b2a9}

/* Quick Actions */
.cr-quick-actions{position:relative}
.cr-quick-action-btn{background:#1a3d1f;border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:.9rem;transition:background .2s}
.cr-quick-action-btn:hover{background:#145222}
.cr-quick-actions-dropdown{position:absolute;top:100%;right:0;background:#fff;border:1px solid #d3d1c7;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.1);width:200px;z-index:1000;opacity:0;visibility:hidden;transform:translateY(-10px);transition:all .2s}
.cr-quick-actions-dropdown.show{opacity:1;visibility:visible;transform:translateY(0)}
.cr-quick-action-item{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;color:#2c2c2a;text-decoration:none;font-size:.85rem;transition:background .2s}
.cr-quick-action-item:hover{background:#f1efe8}
.cr-quick-action-item i{width:16px;text-align:center}

@media(max-width:640px){
  .cr-row-3,.cr-row-half{grid-template-columns:1fr}
  .cr-top-header{flex-direction:column;align-items:flex-start;gap:12px}
  .cr-header-right{width:100%;justify-content:space-between}
  .cr-muni-grid{grid-template-columns:repeat(2,1fr)}
  .cr-inv-row{grid-template-columns:1fr auto}
  .cr-inv-res{display:none}
}
</style>

<div class="cr-wrap">

  {{-- Errors --}}
  @if($errors->any())
  <div class="cr-error">
    <i class="fas fa-exclamation-circle"></i>
    <div>
      <strong>Fix the following:</strong>
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  </div>
  @endif

  <form method="POST" action="{{ route('staff.relief.store') }}" class="cr-form">
    @csrf

    {{-- Event details --}}
    <div class="cr-card">
      <div class="cr-sec-label">Event details</div>
      <div class="cr-body">
        <div class="cr-row cr-row-3">
          <div class="cr-field">
            <label>Event name <span class="cr-req">*</span></label>
            <input type="text" name="name" value="{{ old('name', $prefillName ?? '') }}" placeholder="e.g. Flood Response – Rizal" required>
          </div>
          <div class="cr-field">
            <label>Date <span class="cr-req">*</span></label>
            <input type="date" name="date" value="{{ old('date', $prefillDate ?? '') }}" required>
          </div>
          <div class="cr-field">
            <label>Venue <span class="cr-req">*</span></label>
            <input type="text" name="venue" value="{{ old('venue') }}" placeholder="e.g. Barangay Hall" required>
          </div>
        </div>
        <div class="cr-row cr-row-half" style="margin-bottom:0">
          <div class="cr-field">
            <label>Calamity type</label>
            <select name="calamity_id">
              <option value="">Select type</option>
              @if(isset($calamities))
                <optgroup label="Natural Calamities">
                  @foreach($calamities->where('type', 'natural') as $calamity)
                  <option value="{{ $calamity->id }}" {{ old('calamity_id')==$calamity->id?'selected':'' }}>{{ $calamity->name }}</option>
                  @endforeach
                </optgroup>
                <optgroup label="Human-Made Calamities">
                  @foreach($calamities->where('type', 'human_made') as $calamity)
                  <option value="{{ $calamity->id }}" {{ old('calamity_id')==$calamity->id?'selected':'' }}>{{ $calamity->name }}</option>
                  @endforeach
                </optgroup>
              @endif
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- Facilitators --}}
    <div class="cr-card">
      <div class="cr-sec-label">Facilitators</div>
      
      
      {{-- Staff --}}
      <div class="cr-acc-group">
        <button type="button" class="cr-acc-toggle" onclick="crToggleAcc('cr-staff')">
          <span class="cr-pill cr-pill-staff">STAFF</span>
          <span class="cr-acc-name">Core team members</span>
          <i class="cr-chev fas fa-chevron-down open"></i>
        </button>
        <div class="cr-acc-body open" id="cr-staff">
          <div class="cr-chip-grid">
            @php 
            // Make role filtering more flexible - try different possible role names
            $staffUsers = $facilitators->filter(function($user) {
                $roleName = strtolower($user->role->name ?? '');
                return $roleName === 'staff' || $roleName === 'staff member' || strpos($roleName, 'staff') !== false;
            }); 
            // Debug: Check staff count
            $staffCount = $staffUsers->count();
            @endphp
            
                        
            @forelse($staffUsers as $f)
              <div class="cr-chip {{ in_array($f->id, old('facilitators_staff',[])) ? 'cr-chip-on' : '' }}"
                onclick="crToggleChip(this,'f-staff-{{ $f->id }}')">
                <input type="checkbox" name="facilitators_staff[]" value="{{ $f->id }}"
                  id="f-staff-{{ $f->id }}" {{ in_array($f->id, old('facilitators_staff',[])) ? 'checked' : '' }}>
                <div class="cr-av">{{ strtoupper(substr($f->first_name,0,1).substr($f->last_name,0,1)) }}</div>
                <div>
                  <div class="cr-chip-name">{{ $f->first_name }} {{ $f->last_name }}</div>
                  <div class="cr-chip-sub">{{ $f->role ? $f->role->name : 'No Role' }}</div>
                </div>
              </div>
            @empty
              <p class="cr-empty">No staff assigned yet.</p>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Volunteers --}}
      <div class="cr-acc-group">
        <button type="button" class="cr-acc-toggle" onclick="crToggleAcc('cr-vol')">
          <span class="cr-pill cr-pill-vol">VOLUNTEERS</span>
          <span class="cr-acc-name">Support volunteers</span>
          <i class="cr-chev fas fa-chevron-down"></i>
        </button>
        <div class="cr-acc-body" id="cr-vol">
          <div class="cr-chip-grid">
            @php 
            // Make role filtering more flexible - try different possible role names
            $volunteerUsers = $facilitators->filter(function($user) {
                $roleName = strtolower($user->role->name ?? '');
                return $roleName === 'volunteer' || $roleName === 'volunteers' || strpos($roleName, 'volunteer') !== false;
            }); 
            // Debug: Check volunteer count
            $volunteerCount = $volunteerUsers->count();
            @endphp
            
                        
            @forelse($volunteerUsers as $f)
            <div class="cr-chip {{ in_array($f->id, old('facilitators_volunteers',[])) ? 'cr-chip-on' : '' }}"
              onclick="crToggleChip(this,'f-vol-{{ $f->id }}')">
              <input type="checkbox" name="facilitators_volunteers[]" value="{{ $f->id }}"
                id="f-vol-{{ $f->id }}" {{ in_array($f->id, old('facilitators_volunteers',[])) ? 'checked' : '' }}>
              <div class="cr-av">{{ strtoupper(substr($f->first_name,0,1).substr($f->last_name,0,1)) }}</div>
              <div>
                <div class="cr-chip-name">{{ $f->first_name }} {{ $f->last_name }}</div>
                <div class="cr-chip-sub">Volunteer</div>
              </div>
            </div>
            @empty
            <p class="cr-empty">No volunteers assigned yet.</p>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Barangay Partners --}}
      <div class="cr-acc-group">
        <button type="button" class="cr-acc-toggle" onclick="crToggleAcc('cr-bgy-fac')">
          <span class="cr-pill cr-pill-bar">BARANGAY</span>
          <span class="cr-acc-name">Local representatives</span>
          <i class="cr-chev fas fa-chevron-down"></i>
        </button>
        <div class="cr-acc-body" id="cr-bgy-fac">
          <div class="cr-chip-grid">
            @php 
            // Make role filtering more flexible - try different possible role names
            $barangayUsers = $facilitators->filter(function($user) {
                $roleName = strtolower($user->role->name ?? '');
                return $roleName === 'barangay partner' || $roleName === 'barangay' || strpos($roleName, 'barangay') !== false;
            }); 
            // Debug: Check what roles are available
            $availableRoles = $facilitators->pluck('role.name')->unique()->implode(', ');
            $barangayCount = $barangayUsers->count();
            @endphp
            
                        
            @forelse($barangayUsers as $f)
            <div class="cr-chip {{ in_array($f->id, old('facilitators_barangay',[])) ? 'cr-chip-on' : '' }}"
              onclick="crToggleChip(this,'f-bar-{{ $f->id }}')">
              <input type="checkbox" name="facilitators_barangay[]" value="{{ $f->id }}"
                id="f-bar-{{ $f->id }}" {{ in_array($f->id, old('facilitators_barangay',[])) ? 'checked' : '' }}>
              <div class="cr-av">{{ strtoupper(substr($f->first_name,0,1).substr($f->last_name,0,1)) }}</div>
              <div>
                <div class="cr-chip-name">{{ $f->first_name }} {{ $f->last_name }}</div>
                {{-- barangay may be a relation object or a plain string --}}
                <div class="cr-chip-sub">
                  @if(is_object($f->barangay))
                    {{ $f->barangay->name ?? 'Barangay Partner' }}
                  @else
                    {{ $f->barangay ?? 'Barangay Partner' }}
                  @endif
                </div>
              </div>
            </div>
            @empty
            <p class="cr-empty">No barangay partners assigned yet.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    {{-- Barangay selection --}}
    <div class="cr-card">
      <div class="cr-sec-label">Select barangays</div>
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
            <span><strong id="cr-benef-count">0</strong> beneficiaries selected</span>
          </div>
        </div>
        <p id="cr-bgy-hint" class="cr-bgy-hint">
          <i class="fas fa-map-marker-alt"></i> Select a municipality above to choose barangays
        </p>
      </div>
    </div>

    {{-- Inventory --}}
    <div class="cr-card">
      <div class="cr-sec-label">
        Inventory distribution
        <span class="cr-sec-aside">
          Beneficiaries: <strong id="cr-inv-benef">0</strong>
          <input type="hidden" id="cr-total-hh" name="total_households" value="0">
        </span>
      </div>
      <div class="cr-body" style="padding-top:0">
        <div class="cr-inv-list">
          @foreach(\App\Models\Category::with('subcategories.items.inventory')->get() as $cat)
            @foreach($cat->subcategories as $sub)
              @foreach($sub->items as $item)
              <div class="cr-inv-row">
                <label class="cr-inv-check">
                  <input type="checkbox" name="distribute_items[]" value="{{ $item->id }}"
                    data-qty="{{ $item->inventory?->quantity ?? 0 }}"
                    data-unit="{{ $item->unit }}"
                    data-name="{{ $item->name }}"
                    onchange="crCalcDist()">
                  <div>
                    <div class="cr-inv-name">{{ $item->name }} ({{ $item->inventory?->quantity ?? 0 }} {{ $item->unit }})</div>
                    <div class="cr-inv-cat">{{ $cat->name }} · {{ $sub->name }}</div>
                  </div>
                </label>
                <div class="cr-inv-res" id="cr-res-{{ $item->id }}">
                  <input type="number" name="item_quantities[{{ $item->id }}]" 
                    min="0" max="{{ $item->inventory?->quantity ?? 0 }}"
                    placeholder="Qty" 
                    class="cr-qty-input"
                    data-max="{{ $item->inventory?->quantity ?? 0 }}"
                    data-unit="{{ $item->unit }}"
                    onchange="crCalcDist()"
                    oninput="crCalcDist()">
                  <span class="cr-calc-display" id="cr-calc-{{ $item->id }}">— per beneficiary</span>
                </div>
              </div>
              @endforeach
            @endforeach
          @endforeach
        </div>
      </div>
    </div>

    {{-- Footer --}}
    <div class="cr-footer">
      <a href="{{ route('staff.relief.index') }}" class="cr-btn-cancel">Cancel</a>
      <button type="submit" class="cr-btn-submit">
        <i class="fas fa-plus"></i> Create relief event
      </button>
    </div>

  </form>
</div>

<script>
/* ── Accordion ───────────────────────────────────── */
function crToggleAcc(id) {
  var body = document.getElementById(id);
  var btn  = body.previousElementSibling;
  var chev = btn.querySelector('.cr-chev');
  var open = body.classList.contains('open');
  body.classList.toggle('open', !open);
  chev.classList.toggle('open', !open);
}

/* ── Facilitator chips ───────────────────────────── */
function crToggleChip(chip, cbId) {
  chip.classList.toggle('cr-chip-on');
  var cb = document.getElementById(cbId);
  if (cb) cb.checked = chip.classList.contains('cr-chip-on');
}

/* ── Municipality tiles ──────────────────────────── */
var crMuniData = @json($municipalities);
var crSelectedMunis = {};

function crToggleMuni(tile, muniId) {
  var cb = document.getElementById('muni-' + muniId);
  if (crSelectedMunis[muniId]) {
    delete crSelectedMunis[muniId];
    tile.classList.remove('cr-muni-on');
    if (cb) cb.checked = false;
  } else {
    var muni = crMuniData.find(function(m){ return m.id === muniId; });
    if (muni) crSelectedMunis[muniId] = muni;
    tile.classList.add('cr-muni-on');
    if (cb) cb.checked = true;
  }
  crRenderBgy();
}

function crRenderBgy() {
  var keys = Object.keys(crSelectedMunis);
  var sec  = document.getElementById('cr-bgy-sec');
  var hint = document.getElementById('cr-bgy-hint');
  var grid = document.getElementById('cr-bgy-grid');

  if (!keys.length) {
    sec.style.display  = 'none';
    hint.style.display = 'flex';
    crUpdateBenef();
    return;
  }
  sec.style.display  = 'block';
  hint.style.display = 'none';

  var html = '';
  keys.forEach(function(id) {
    var muni = crSelectedMunis[id];
    if (muni && muni.barangays) {
      muni.barangays.forEach(function(b) {
        var ct = b.beneficiary_count || 0;
        html += '<div class="cr-bgy-chip" data-b="' + ct + '" onclick="crToggleBgy(this)">'
              + '<div class="cr-bgy-name">' + b.name + '</div>'
              + '<div class="cr-bgy-ct">' + ct + ' beneficiaries</div>'
              + '<input type="hidden" name="barangay_ids[]" value="' + b.id + '" disabled>'
              + '</div>';
      });
    }
  });
  grid.innerHTML = html || '<span style="font-size:13px;color:#9ca3af">No barangays found.</span>';
  crUpdateBenef();
}

function crToggleBgy(chip) {
  chip.classList.toggle('cr-bgy-on');
  var inp = chip.querySelector('input[type=hidden]');
  if (inp) inp.disabled = !chip.classList.contains('cr-bgy-on');
  crUpdateBenef();
}

function crUpdateBenef() {
  var total = 0;
  document.querySelectorAll('.cr-bgy-chip.cr-bgy-on').forEach(function(c) {
    total += parseInt(c.dataset.b) || 0;
  });
  document.getElementById('cr-benef-count').textContent = total;
  document.getElementById('cr-inv-benef').textContent   = total;
  document.getElementById('cr-total-hh').value          = total;
  var pill = document.getElementById('cr-benef-pill');
  pill.style.display = total > 0 ? 'inline-flex' : 'none';
  crCalcDist();
}

/* ── Inventory calc ──────────────────────────────── */
function crCalcDist() {
  var hh = parseInt(document.getElementById('cr-total-hh').value) || 0;
  
  // Update all item calculations
  document.querySelectorAll('input[name="distribute_items[]"]').forEach(function(cb) {
    var id = cb.value;
    var maxQty = parseInt(cb.dataset.qty) || 0;
    var unit = cb.dataset.unit;
    var qtyInput = document.querySelector('input[name="item_quantities[' + id + ']"]');
    var calcDisplay = document.getElementById('cr-calc-' + id);
    
    if (!calcDisplay) return;
    
    var qty = qtyInput ? parseInt(qtyInput.value) || 0 : 0;
    
    if (cb.checked && hh > 0 && qty > 0) {
      var per = Math.floor(qty / hh);
      calcDisplay.textContent = per + ' ' + unit + ' each (' + qty + ' ÷ ' + hh + ' = ' + per + ' per beneficiary)';
      calcDisplay.style.color = '#065f46';
    } else if (cb.checked && hh === 0) {
      calcDisplay.textContent = 'No beneficiaries selected';
      calcDisplay.style.color = '#e24b4a';
    } else if (cb.checked && qty === 0) {
      calcDisplay.textContent = 'Enter quantity';
      calcDisplay.style.color = '#e24b4a';
    } else {
      calcDisplay.textContent = '— per beneficiary';
      calcDisplay.style.color = '#888780';
    }
  });
}

// Combine facilitator arrays before form submission
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form.cr-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      // Prevent default to ensure we can modify the form
      e.preventDefault();
      
      // Combine all facilitator arrays
      const staffFacilitators = Array.from(document.querySelectorAll('input[name="facilitators_staff[]"]:checked'))
        .map(input => input.value);
      const volunteerFacilitators = Array.from(document.querySelectorAll('input[name="facilitators_volunteers[]"]:checked'))
        .map(input => input.value);
      const barangayFacilitators = Array.from(document.querySelectorAll('input[name="facilitators_barangay[]"]:checked'))
        .map(input => input.value);
      
      // Combine all facilitator IDs
      const allFacilitators = [...staffFacilitators, ...volunteerFacilitators, ...barangayFacilitators];
      
      // Remove any existing facilitator_ids inputs
      document.querySelectorAll('input[name="facilitator_ids[]"]').forEach(input => input.remove());
      
      // Create hidden inputs for each facilitator ID (array format)
      allFacilitators.forEach(function(userId) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'facilitator_ids[]';
        hiddenInput.value = userId;
        form.appendChild(hiddenInput);
      });
      
      // Remove the individual arrays to avoid confusion
      document.querySelectorAll('input[name="facilitators_staff[]"], input[name="facilitators_volunteers[]"], input[name="facilitators_barangay[]"]')
        .forEach(input => input.remove());
      
      // Submit the form programmatically
      form.submit();
    });
  }
});

</script>

@endsection